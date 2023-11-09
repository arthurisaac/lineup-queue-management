<?php

namespace App\Http\Controllers\api;

use App\Models\Service;
use App\Models\TicketTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TicketTimeController extends Controller
{
    public function timeApproximation(Request $request) {

        $validator = Validator::make($request->all(), [
            //'hour' => 'required|numeric',
            //'minute' => 'required|numeric',
            'time' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies étaient invalides.' . $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }
        
        //$hour = $request->get("hour");
        //$minute = $request->get("minute");


        //$appro = Carbon::now()->addHours($hour)->addMinutes($minute);
        $appro = Carbon::parse($request->get('time'));
        return response()->json(["message"=> "Temps approximatif $appro", "passage" => $appro->format('H:i'),]);

    }
    
    public function saveTimeTicket(Request $request) {

        $validator = Validator::make($request->all(), [
            'service_id' => 'required|numeric',
            'user' => 'required|numeric',
            //'hour' => 'required',
            //'minute' => 'required',
            'minute' => 'time',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies étaient invalides.' . $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $passage = Carbon::parse($request->get('time'));
        /* $passage = Carbon::now()
        ->addHours($request->get('hour'))
        ->addMinutes($request->get('minute')); */
        
        $timeTicket = new TicketTime([
            'service_id'=> $request->get('service_id'),
            'user'=> $request->get('user'),
            'passage'=> $passage->toDateTimeString(),
        ]);
        $timeTicket->save();

        return response()->json(["message"=> "Temps de passage enregistré"]);

    }

    public function availableTime(Request $request) {

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
            $lastTicket = \App\Models\Ticket::query()
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

            
            $averageTimeInMillis = $service->temps_moyen * $service->restant;
            $averageTimeInSeconds = $averageTimeInMillis / 1000;
            
            //dd($averageTimeInSeconds);
            // Available time + 1hour in second
            $avaibleStartTimeInSecond = $averageTimeInSeconds + 3600;

            $debut = Carbon::now()->addSeconds($avaibleStartTimeInSecond);
            //echo $debut->toTimeString() . '<br/>';
            
            $fin = Carbon::now()->setHour(18)->setMinute(0)->setSecond(0);
            //echo $fin->toTimeString() . '<br>';
            
            // Si la date actuelle est inferieur a 16h
            if(Carbon::now()->lt($fin)) {
                //$diffInSeconds  = $fin->diffInRealSeconds(Carbon::now());
                //echo $diffInSeconds . '<br>';

                //$endTime = Carbon::now()->addSeconds($diffInSeconds);
                //echo $endTme->toTimeString() . '<br>';

                // Liste des timetickets du jour
                $ticketTime = TicketTime::query()
                ->where('passage', '>', Carbon::now()->format('Y-m-d H:i:s'))
                ->get();

                $heureDebut = $debut->format('H:i');
                $heureFin = $fin->format('H:i');

                // Liste des heures disponible
                $disponible = $this->heuresMinutesDisponibles($heureDebut, $heureFin);

                // On supprime les heures de passage des heures disponibles
                foreach ($ticketTime as $ticketTimeItem) {
                    $passage = Carbon::parse($ticketTimeItem->passage)->format('H:i');
                    foreach ($disponible as $k => $v) {
                        if ($v['approximation'] === $passage) {
                            unset($disponible[$k]);
                        }
                    }
                }


                return response()->json(["message"=> "Temps", "tickets" => $ticketTime, "disponible" => array_values($disponible), "status" => true]);
            } else {
                return response()->json(["message"=> "Temps non disponible", "disponible" => [], "status" => false]);
            }


        }


    }
    function heuresMinutesDisponibles($heureDebut, $heureFin, $intervalleMinutes = 5) {
        $listeHeuresMinutes = [];

    $heureActuelle = Carbon::parse($heureDebut);
    $heureFin = Carbon::parse($heureFin);
    $now = Carbon::now();

    while ($heureActuelle < $heureFin) {
        $diffInSeconds = $heureActuelle->diffInSeconds($now);

        $init = $diffInSeconds;
        $day = floor($init / 86400);
        $hours = floor(($init -($day*86400)) / 3600);
        $minutes = floor(($init / 60) % 60);
        $moment =  "$hours:$minutes";

        $listeHeuresMinutes[] = array("approximation" => $heureActuelle->format('H:i'), "moment" => $moment);
        $heureActuelle->addMinutes($intervalleMinutes);
    }

    return $listeHeuresMinutes;
    }
}
