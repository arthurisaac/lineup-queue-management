<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan; 

class ExecuteArtisanCommingFromBrowserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $command)
        {
        $params = $request->all();
        Artisan::call($command, $params);
        $output = Artisan::output();
        echo 'Output of given command: ' . $output;
        }
}
