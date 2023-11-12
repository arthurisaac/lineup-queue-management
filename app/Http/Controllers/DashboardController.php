<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index() {
        /* if (Auth::user()->roles()->where('name', 'caller')->exists()) {
            return to_route('caller');
        } */
        return Inertia::render('Dashboard');
    }
}
