<?php

use App\Http\Controllers\PublicSiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicSiteController::class, 'home'])->name('home');
Route::get('/terminos-y-condiciones', [PublicSiteController::class, 'terms'])->name('terms');
Route::get('/politica-de-privacidad', [PublicSiteController::class, 'privacy'])->name('privacy');
Route::get('/libro-de-reclamaciones', [PublicSiteController::class, 'complaintForm'])->name('complaints.form');
Route::post('/libro-de-reclamaciones', [PublicSiteController::class, 'storeComplaint'])
    ->middleware('throttle:3,60')->name('complaints.store');
Route::get('/libro-de-reclamaciones/constancia/{token}', [PublicSiteController::class, 'complaintReceipt'])
    ->name('complaints.receipt');
Route::get('/solicitar-eliminacion-cuenta', [PublicSiteController::class, 'deletionForm'])->name('deletion.form');
Route::post('/solicitar-eliminacion-cuenta', [PublicSiteController::class, 'storeDeletionRequest'])
    ->middleware('throttle:3,60')->name('deletion.store');
