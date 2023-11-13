<?php

namespace App\Http\Controllers;

use App\Events\BorneEvent;
use App\Events\PusherBroadcast;
use App\Models\Passage;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CallerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::query()->whereNotNull("guichet")->whereNot('id', auth()->user()->id)->get();
         $today = \Carbon\Carbon::today()->toDateString();
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
        
            $passage = Passage::query()
                ->with("Service")
                ->with("Ticket")
                ->where("guichet", auth()->user()->guichet ?? auth()->user()->id)
                ->orderBy("created_at","desc")
                ->first();
       
        return Inertia::render('Client/Caller', [
            'services' => $services,
            'passage' => $passage,
            'users' => $users,
        ]);
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

    public function nextTicket(Request $request) {
        $request->validate([
            'service' => 'required',
        ]);

        $today = \Carbon\Carbon::today()->toDateString();

        $service = $request->get('service');
        if ($service == 'any') {
            $lastTicket = Ticket::query()
                ->select('tickets.id', 'numero', 'service', 'service_id', 'prefix')
                ->join('services as s', 'tickets.service', '=', 's.nom')
                ->where('tickets.created_at', '>=', $today)
                ->where('tickets.created_at', '<=', now())
                ->where('tickets.etat', '=', 0)
                ->orderBy('tickets.id')
                ->first();
        } else {
            $lastTicket = Ticket::query()
                ->select('tickets.id', 'numero', 'service', 'service_id', 'prefix')
                ->join('services as s', 'tickets.service', '=', 's.nom')
                ->where('tickets.created_at', '>=', $today)
                ->where('tickets.created_at', '<=', now())
                ->where('tickets.etat', '=', 0)
                ->where('tickets.service_id', '=', $service)
                ->orderBy('tickets.id')
                ->first();
        }

            if ($lastTicket) {
                //dd($lastTicket);
                $lastTicket->guichet = auth()->user()->guichet ?? auth()->user()->id;
                $lastTicket->etat = 1;
                $lastTicket->save();  
                
                $passage = new Passage([
                    'guichet' => auth()->user()->guichet ?? auth()->user()->id,
                    'service' => $lastTicket->service_id,
                    'ticket' => $lastTicket->id,
                ]);
                $passage->save();
                event(new BorneEvent('next-ticket'));
            }
            
            return redirect()->back()->with('success','Ticket suivant');
    }

    public function recallTicket(Request $request) {
        event(new BorneEvent('recall-ticket'));
        return redirect()->back()->with('success','Rappeler le numero sur la TV');
    }

    public function transfertTicket(Request $request) {
        $request->validate([
            'guichet' => 'required',
            'ticket' => 'required',
        ]);
        $ticket = Ticket::find($request->ticket);

        if ($ticket) {
            $passage = new Passage([
                'guichet' => $request->get('guichet'),
                'service' => $ticket->service_id,
                'ticket' => $ticket->id,
            ]);
            $passage->save();
            event(new BorneEvent('next-ticket'));
        }

        return redirect()->back()->with('success','Ticket suivant');
    }
}
