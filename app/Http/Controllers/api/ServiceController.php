<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
         $today = Carbon::today()->toDateString();
         $services = \DB::table('services')
            ->select('services.id', 'services.nom', 'services.photo', 'services.temps', 'services.prefix')
            ->selectRaw('COUNT(DISTINCT tickets.id) AS restant')
            ->leftJoin('tickets', function ($join) use ($today) {
                $join->on('services.id', '=', 'tickets.service_id')
                    ->where('tickets.etat', '=', 0)
                    ->whereBetween('tickets.created_at', [$today, now()]);
            })
            ->groupBy('services.id')
            ->get();
        
       
        return response()->json(['services' => $services]);
    }

    public function show($id)
    {
         $today = Carbon::today()->toDateString();
         $service = Service::query()
            //->select('services.id', 'services.nom', 'services.photo', 'services.temps', 'services.prefix')
            ->where('services.id', $id)
            ->selectRaw('services.id, services.nom, services.photo, services.prefix, services.temps_moyen, COUNT(DISTINCT tickets.id) AS restant')
            ->leftJoin('tickets', function ($join) use ($today) {
                $join->on('services.id', '=', 'tickets.service_id')
                    ->where('tickets.etat', '=', 0)
                    ->whereBetween('tickets.created_at', [$today, now()]);
            })
            ->groupBy('services.id')
            ->first();

            $lastTicket = Ticket::query()
            ->whereHas('Service', function ($query) use ($service) {
                $query->where('id', $service->id);
            })
            ->whereBetween('created_at', [Carbon::today()->toDateString(), NOW()])
            ->orderBy('id','desc')
            ->first();

            $queue = 0;
            $averageTimeInMillis = $service->temps_moyen * $service->restant;
            $averageTimeInSeconds = $averageTimeInMillis / 1000;

            if ($lastTicket) {
                $queue = $lastTicket->numero;
                $queue++;
            }


            $init = $averageTimeInSeconds;
            $day = floor($init / 86400);
            $hours = floor(($init -($day*86400)) / 3600);
            $minutes = floor(($init / 60) % 60);
            //$seconds = $init % 60;
            $average =  "$hours H $minutes MN";

            $passage = Carbon::now()->addSeconds($averageTimeInSeconds);
            
        
        return response()->json(['service' => $service, 'numero' => $queue, "average" => $average, "passage" => $passage->format('H:i')]);
    }
}
