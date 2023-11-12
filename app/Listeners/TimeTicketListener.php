<?php

namespace App\Listeners;

use App\Events\TimeTicketEvent;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\TicketTime;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TimeTicketListener
{
    /**
    * Create the event listener.
    */
    public function __construct()
    {
        //
    }
    
    /**
    * Handle the event.
    */
    public function handle(TimeTicketEvent $event): void
    {
        $timetickets = TicketTime::query()
        ->whereBetween("created_at", [Carbon::today()->setHour(0)->setMinute(0), Carbon::today()->setHour(23)->setMinute(59)])
        ->whereNull("ticket_id")
        ->get();
        
        foreach ($timetickets as $time) {
            $today = Carbon::today()->toDateString();
            $service = $service = Service::query()
            ->where('services.id', $time->service_id)
            ->selectRaw('services.id, services.nom, services.photo, services.prefix, services.temps_moyen, COUNT(DISTINCT tickets.id) AS restant')
            ->leftJoin('tickets', function ($join) use ($today) {
                $join->on('services.id', '=', 'tickets.service_id')
                ->where('tickets.etat', '=', 0)
                ->whereBetween('tickets.created_at', [$today, now()]);
            })
            ->groupBy('services.id')
            ->first();
            
            $passage = Carbon::parse($time->passage);
            
            if ($passage->lt(Carbon::now()->addMilliseconds($service->temps_moyen))) {
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
                
                $time->ticket_id = $newTicket->id;
                $time->save();
                
                $post = [
                    'service_id' => $service->id,
                    'ticket_id' => $newTicket->id,
                    'service' => $service->nom,
                    'ticket' => $newTicket->numero,
                    'agence' => $time->agence,
                    'user' => $time->user,
                ];
                $ch = curl_init(env("CLIENT_URL", "http://localhost") . '/api/ticket');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                $response = curl_exec($ch);
                echo $response;
                curl_close($ch);
            }
        }
    }
}
