<?php

use App\Events\MessageCreated;
use App\Http\Controllers\UserController;
use App\Models\Room;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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

// auth()->loginUsingId(1);

Route::get('/test', UserController::class);

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // +++> Room
    Route::prefix('/rooms')->group(function () {
        Route::get('', function () {
            $rooms = Room::with(['participants:id'])->get(['id', 'title']);
            return Inertia::render('Rooms/Dashboard', [
                'rooms' => $rooms
            ]);
        })->name('rooms.index');

        Route::get('{room}', function (Room $room) {
            $room->load(['messages:id,room_id,body', 'participants:id,name,email']);
            return Inertia::render('Rooms/Show', [
                'room' => $room
            ]);
        })->name('rooms.show');
    });

    // +++> Message
    Route::prefix('/rooms/{room}/messages')->group(function () {
        Route::post('', function (Room $room) {
            request()->validate([
                'message' => ['required', 'string', 'max:255']
            ]);

            $message = $room->messages()->create([
                'user_id' => auth()->id(),
                'body' => request('message')
            ]);

            event(new MessageCreated($message));

            return redirect()->route('rooms.show', $room)->with('success', 'Message created.');
        })->name('messages.store');
    });
});
