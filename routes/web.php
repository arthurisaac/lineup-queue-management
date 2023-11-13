<?php

use App\Events\TicketAlmostArrivedEvent;
use App\Http\Controllers\BorneHomeController;
use App\Http\Controllers\CallerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExecuteArtisanCommingFromBrowserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TVHomeController;
use App\Http\Controllers\UserController;
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

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified', 'role:admin'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/borne', [BorneHomeController::class, 'index'])->name('borne');
Route::get('/tv', [TVHomeController::class, 'index'])->name('tv');
Route::get('/caller', [CallerController::class, 'index'])->middleware(['auth', 'verified', 'role:caller'])->name('caller');

Route::post('/next-ticket', [CallerController::class, 'nextTicket'])->name('nextTicket');
Route::post('/recall-ticket', [CallerController::class, 'recallTicket'])->name('recallTicket');
Route::post('/transfert-ticket', [CallerController::class, 'transfertTicket'])->name('transfertTicket');

Route::group(['prefix' => 'ticket',], function () {
    Route::post('/new', [BorneHomeController::class, 'newTicket'])->name('ticket.create');
});


Route::group(['middleware' => ['auth', 'role:admin'], 'prefix' => 'manage',], function () {
    Route::resource('users', UserController::class);
    Route::post('add-user-role', [UserController::class, 'updateUserRole'])->name('users.add-user-role');
});

Route::get('/run-command/{name_of_command}', ExecuteArtisanCommingFromBrowserController::class);

Route::get('passage', function() {
        //event(new TimeTicketEvent());
        //event(new TicketAlmostArrivedEvent());
});

require __DIR__.'/auth.php';
