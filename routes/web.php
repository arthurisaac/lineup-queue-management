<?php

use App\Http\Controllers\BorneHomeController;
use App\Http\Controllers\CallerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketTimeController;
use App\Http\Controllers\TVHomeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/borne', [BorneHomeController::class, 'index'])->name('borne');
Route::get('/tv', [TVHomeController::class, 'index'])->name('tv');
Route::get('/caller', [CallerController::class, 'index'])->middleware(['auth', 'verified'])->name('caller');

Route::post('/next-ticket', [CallerController::class, 'nextTicket'])->name('nextTicket');
Route::post('/recall-ticket', [CallerController::class, 'recallTicket'])->name('recallTicket');

Route::group(['prefix' => 'ticket',], function () {
    Route::post('/new', [BorneHomeController::class, 'newTicket'])->name('ticket.create');
});

require __DIR__.'/auth.php';
