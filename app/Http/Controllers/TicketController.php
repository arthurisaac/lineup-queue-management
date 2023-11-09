<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
         $validator = Validator::make($request->all(), [
            'service_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies étaient invalides.' . $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $service = Service::query()->find($request->service_id);
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

        return response()->json(["message" => '.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
