<?php

namespace App\Http\Controllers;

use App\Events\BorneEvent;
use App\Models\Borne;
use App\Models\Service;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BorneHomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $borne = Borne::query()->first();
        $services = DB::table('services')
            ->select('services.id', 'services.nom', 'services.photo', 'services.temps', 'services.prefix')
            ->selectRaw('COUNT(DISTINCT tickets.id) AS restant')
            ->leftJoin('tickets', function ($join) use ($today) {
                $join->on('services.id', '=', 'tickets.service_id')
                    ->where('tickets.etat', '=', 0)
                    ->whereBetween('tickets.created_at', [$today, now()]);
            })
            ->groupBy('services.id')
            ->get();
       
        return Inertia::render('Client/Borne', [
            'status' => '125',
            'borne' => $borne,
            'services' => $services,
            'today' => $today,
        ]);
    }
    

    public function newTicket(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        $service = Service::query()->find($request->id);
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
        }
        //event(new BorneEvent('new-ticket'));
        return redirect()->back()->with('success','Nouveau ticket tire');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
