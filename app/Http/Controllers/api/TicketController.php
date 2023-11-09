<?php

namespace App\Http\Controllers\api;

use App\Events\BorneEvent;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
     public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'service_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies étaient invalides.' . $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $today = Carbon::today()->toDateString();
        $service = Service::query()
            ->where('services.id', $request->service_id)
            ->selectRaw('services.id, services.nom, services.photo, services.prefix, services.temps_moyen, COUNT(DISTINCT tickets.id) AS restant')
            ->leftJoin('tickets', function ($join) use ($today) {
                $join->on('services.id', '=', 'tickets.service_id')
                    ->where('tickets.etat', '=', 0)
                    ->whereBetween('tickets.created_at', [$today, now()]);
            })
            ->groupBy('services.id')
            ->first();
        if ($service) {
            $lastTicket = Ticket::query()
            ->whereHas('Service', function ($query) use ($service) {
                $query->where('id', $service->id);
            })
            ->whereBetween('created_at', [Carbon::today()->toDateString(), NOW()])
            ->orderBy('id','desc')
            ->first();

            $queue = 0;
            if ($lastTicket) {
                $queue = $lastTicket->numero;
                $queue++;
            }

            $newTicket = new Ticket();
            $newTicket->code = mt_rand(1111,9999);
            $newTicket->numero = $queue;
            $newTicket->service = $service->nom;
            $newTicket->service_id = $service->id;

            $newTicket->save();

            $averageTimeInMillis = $service->temps_moyen * $service->restant;
            $averageTimeInSeconds = $averageTimeInMillis / 1000;

            $init = $averageTimeInSeconds;
            $day = floor($init / 86400);
            $hours = floor(($init -($day*86400)) / 3600);
            $minutes = floor(($init / 60) % 60);
            //$seconds = $init % 60;
            $average =  "$hours H $minutes MN";

            $passage = Carbon::now()->addSeconds($averageTimeInSeconds);

            //event(new BorneEvent('new-ticket'));
            return response()->json(["message" => 'Nouveau ticket', "status" => true, "numero" => $queue, "service_id" => $newTicket->service_id, "ticket_id" => $newTicket->id, "service" => $service->nom, "average" => $average, "passage" => $passage->format('H:i')]);
        } else {
            return response()->json(["message"=> "Service non trouvé", "status" => false,]);
        }
        
    }

    public function show($id) {
        $ticket = Ticket::find($id);
        $today = Carbon::today()->toDateString();

        if ($ticket) {
            $service = Service::query()
                //->select('services.id', 'services.nom', 'services.photo', 'services.temps', 'services.prefix')
                ->where('services.id', $ticket->service_id)
                ->selectRaw('services.id, services.nom, services.photo, services.prefix, services.temps_moyen, COUNT(DISTINCT tickets.id) AS restant')
                ->leftJoin('tickets', function ($join) use ($today, $ticket) {
                    $join->on('services.id', '=', 'tickets.service_id')
                        ->where('tickets.etat', '=', 0)
                        ->where('tickets.numero', '<', $ticket->numero)
                        ->whereBetween('tickets.created_at', [$today, now()]);
                })
                ->groupBy('services.id')
                ->first();

            $averageTimeInMillis = $service->temps_moyen * $service->restant;
            $averageTimeInSeconds = $averageTimeInMillis / 1000;

            $init = $averageTimeInSeconds;
            $day = floor($init / 86400);
            $hours = floor(($init -($day*86400)) / 3600);
            $minutes = floor(($init / 60) % 60);
            //$seconds = $init % 60;
            $average =  "$hours H $minutes MN";

            $passage = Carbon::now()->addSeconds($averageTimeInSeconds);

            return response()->json(["message"=> "Ticket trouve", "ticket" => $ticket, "status" => true,  "average" => $average, "passage" => $passage->format('H:i')]);
        }
        return response()->json(["message"=> "Ticket non trouve", "status" => false]);
    }
}
