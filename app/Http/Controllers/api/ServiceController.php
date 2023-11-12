<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\TicketTime;
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

            // Liste des times tickets
            $timetickets = TicketTime::query()
                ->whereBetween("created_at", [Carbon::today()->setHour(0)->setMinute(0), Carbon::today()->setHour(23)->setMinute(59)])
                ->where("service_id", $service->id)
                ->whereNull("ticket_id")
                ->get();

            $average =  $this->average($averageTimeInSeconds);

            $passage = Carbon::now()->addRealSeconds($averageTimeInSeconds);

            $pendingTickets = 1;
            
            foreach ($timetickets as $ticket) {
                $ticketPassage = Carbon::parse($ticket->passage);
                if ($passage->addMinutes(2)->gt($ticketPassage)) {
                    $queue++;
                    $passage->addMilliseconds($service->temps_moyen);
                    //echo $passage->format("H:i");
    
                    $averageTimeInMillis = $service->temps_moyen * ($service->restant + $pendingTickets);
                    $averageTimeInSeconds = $averageTimeInMillis / 1000;
                    
                    $average =  $this->average($averageTimeInSeconds);
                }
            }
            
        
        return response()->json(['service' => $service, 'numero' => $queue, "average" => $average, "passage" => $passage->format('H:i')]);
    }

    public function average($timeInSeconds) {
        $day = floor($timeInSeconds / 86400);
        $hours = floor(($timeInSeconds -($day*86400)) / 3600);
        $minutes = floor(($timeInSeconds / 60) % 60);

        return "$hours H $minutes MN";
    }
}
