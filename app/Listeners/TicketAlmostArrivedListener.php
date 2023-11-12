<?php

namespace App\Listeners;

use App\Events\TicketAlmostArrivedEvent;
use App\Models\Service;
use App\Models\Ticket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;

class TicketAlmostArrivedListener
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
    public function handle(TicketAlmostArrivedEvent $event): void
    {
        $tickets = Ticket::query()
        ->whereBetween("created_at", [Carbon::today()->setHour(0)->setMinute(0), Carbon::today()->setHour(23)->setMinute(59)])
        ->get();

        foreach ($tickets as $ticket) {

            $today = Carbon::today()->toDateString();
            $service = Service::query()
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

            if ($service) {
                $averageTimeInMillis = $service->temps_moyen * $service->restant;
                $averageTimeInSeconds = $averageTimeInMillis / 1000;

                $passage = Carbon::now()->addRealSeconds($averageTimeInSeconds);

                if ($passage->diffInRealMinutes(Carbon::now()) == 5)  {
                    $post = [
                        'ticket_id' => $ticket->id,
                    ];
                    $ch = curl_init(env("CLIENT_URL", "http://localhost") . '/api/ticket-almost-arrived');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                    $response = curl_exec($ch);
                    echo $response;
                    curl_close($ch);
                }

                
            }
        }
        
    }
}
