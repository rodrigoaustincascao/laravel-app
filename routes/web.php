<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/todo', function () {
    return view('livewire.todo-list.todo-app');
});

// Route::get('/auth/redirect', [LoginController::class, 'redirect']);
 
// Route::get('/auth/callback', [LoginController::class, 'callback']);

Route::get('/auth/redirect', function(){
    return Socialite::driver('github')->redirect();
});

Route::get('/auth/callback', function(){
    $gitHubUser = Socialite::driver('github')->user();
    $user = User::updateOrCreate(
        [
            'github_id' => $gitHubUser->id,
        ],
        [
            'name' => $gitHubUser->name,
            'email' => $gitHubUser->email,
            'github_token' => $gitHubUser->token,
            'github_refresh_token' => $gitHubUser->refreshToken,
        ]
    );

    Auth::login($user);
    return redirect('/dashboard');

});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
