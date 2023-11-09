<?php

namespace App\Http\Controllers;

use App\Models\Information;
use App\Models\Media;
use App\Models\Passage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TVHomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $videos = Media::query()->get();
        $information = Information::query()->first();
        $passages = Passage::query()
        ->with("Service")
        ->with("Ticket")
        ->orderBy("created_at","desc")
        ->limit(2)->get();

        return Inertia::render('Client/Tv', [
            'videos' => $videos,
            'information' => $information,
            'passages' => $passages,
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
}
