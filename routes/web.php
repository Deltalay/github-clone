<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Models\Repo;
use App\Models\User;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
// Prevent / from show error by adding this route
Route::get("/dashboard", function () {
    return Inertia::render("Dashboard");
})->name('dashboard');
// Route::get('/home', function () {
//     return Inertia::render('Home');
// })->middleware(['auth', 'verified'])->name('home');

// Route::middleware(['auth', 'verified'])->group(function () {
//     // Route::get('/new', Inertia::render('New'))->name('new');
//     Route::get('/home', Inertia::render('Home'))->name('home');
// });


Route::middleware(['auth', 'verified'])->group(function () {
    // Putting repos so it can handle when user load
    Route::get('/', function () {
        return Inertia::render('Home', [
            'repos' =>
            Repo::select('name', 'id')
                ->addSelect([
                    'user_name' => User::select('name')
                        ->whereColumn('id', 'repos.user_id')
                ])
                ->limit(5)
                ->get()
        ]);
    })->name('home');
    // Route::get('home', function() {
    //     return Inertia::render('Home');
    // })->name('home');

    Route::get('/new', [RepoController::class, 'createRepo'])->name('new');
    Route::get('/home', [RepoController::class, 'getRepo'])->name('repos');
    // Route::get('/home', [RepoController::class, 'getRepo'])->name('repos');
    Route::post('/store/repository', [RepoController::class, 'store'])->name('repos.store');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
