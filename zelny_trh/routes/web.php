<?php

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsModerator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    UzivatelController, NabidkaController, ObjednavkaController,
    AtributController, KategorieController, ProfileController, 
    HodnotaAtributuController, HomeController, HodnoceniController, 
    AdminController, ModeratorController, DashboardController
};

Route::get('/', [HomeController::class, 'index'])->name('home');

// Resource routes
Route::resource('nabidky', NabidkaController::class);
Route::resource('objednavky', ObjednavkaController::class);
Route::resource('atributy', AtributController::class);
Route::resource('uzivatele', UzivatelController::class);
Route::resource('kategorie', KategorieController::class);
Route::resource('hodnoty', HodnotaAtributuController::class);
Route::resource('hodnoceni', HodnoceniController::class);

Route::get('/categories/{id}/subcategories', [KategorieController::class, 'getSubcategories']);


// Authentication routes
Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::put('/objednavky/{id}/update-farmer', [ObjednavkaController::class, 'updateForFarmer'])->name('objednavky.updateForFarmer');
});

Route::middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/kategorie/manage', [KategorieController::class, 'manage'])->name('admin.kategorie.manage');
    Route::post('/admin/categories/{id}/update-status', [AdminController::class, 'updateCategoryStatus'])->name('admin.updateCategoryStatus');
    Route::get('/admin/uzivatele/manage', [UzivatelController::class, 'manage'])->name('admin.uzivatele.manage');
    Route::get('/admin/nabidky/manage', [NabidkaController::class, 'manage'])->name('admin.nabidky.manage');
    Route::get('/admin/atributy/manage', [AtributController::class, 'manage'])->name('admin.atributy.manage');
    Route::get('/admin/objednavky/manage', [ObjednavkaController::class, 'manage'])->name('admin.objednavky.manage');
    Route::get('/admin/hodnoceni/manage', [HodnoceniController::class, 'manage'])->name('admin.hodnoceni.manage');
});

Route::middleware(['auth', IsModerator::class])->group(function () {
    Route::get('/moderator/dashboard', [ModeratorController::class, 'dashboard'])->name('moderator.dashboard');
    Route::get('/moderator/kategorie/manage', [KategorieController::class, 'manage'])->name('moderator.kategorie.manage');
    Route::post('/moderator/categories/{id}/update-status', [ModeratorController::class, 'updateCategoryStatus'])->name('moderator.updateCategoryStatus');
    Route::get('/moderator/atributy/manage', [AtributController::class, 'manage'])->name('moderator.atributy.manage');
    Route::get('/moderator/hodnoceni/manage', [HodnoceniController::class, 'manage'])->name('moderator.hodnoceni.manage');
});


Route::post('/uzivatele', [UzivatelController::class, 'store'])->name('uzivatele.store');


Route::get('/home', function () {
    return redirect('/');
});



Route::get('/check-auth', function () {
    return Auth::check() ? 'Logged in as ' . Auth::user()->email : 'Guest';
});

Route::get('/test-login', function () {
    // Replace with a valid user identifier from your database
    Auth::loginUsingId('farmer1'); // Example identifier (assuming `prihlasovaci_jmeno` is the primary key)
    return redirect('/');
});

Route::get('/debug-user', function () {
    return Auth::user(); // Display all fields for the authenticated user
});


Route::get('/debug-session', function () {
    return [
        'session_id' => session()->getId(),
        'authenticated_user' => Auth::check() ? Auth::user()->email : 'Guest'
    ];
});

Route::post('/nabidky/{id_nabidky}/pridat-objednavku', [NabidkaController::class, 'pridatObjednavku'])
    ->name('nabidky.pridatObjednavku');
