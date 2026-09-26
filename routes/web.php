<?php

use App\Http\Controllers\CommercialController;
use App\Http\Controllers\AccessPortalController;
use App\Http\Controllers\MinkaPanelController;
use App\Http\Controllers\PublicSiteController;
use Illuminate\Support\Facades\Route;

$panelRoutes = function (): void {
    Route::get('/login', [MinkaPanelController::class, 'loginForm'])->name('minka.login')->middleware('guest');
    Route::post('/login', [MinkaPanelController::class, 'login'])->name('minka.login.store')->middleware('throttle:5,1');
    Route::middleware('auth')->group(function (): void {
        Route::get('/', [MinkaPanelController::class, 'dashboard'])->name('minka.dashboard');
        Route::get('/solicitudes/{id}', [MinkaPanelController::class, 'show'])->name('minka.application');
        Route::post('/solicitudes/{id}/aprobar', [MinkaPanelController::class, 'approve'])->name('minka.application.approve');
        Route::post('/solicitudes/{id}/activar', [MinkaPanelController::class, 'activate'])->name('minka.application.activate');
        Route::post('/solicitudes/{id}/rechazar', [MinkaPanelController::class, 'reject'])->name('minka.application.reject');
        Route::post('/contactos/{id}/estado', [MinkaPanelController::class, 'updateLead'])->name('minka.lead.status');
        Route::post('/logout', [MinkaPanelController::class, 'logout'])->name('minka.logout');
    });
};

if (app()->environment('local', 'testing')) {
    Route::prefix('panel-minka')->group($panelRoutes);
} else {
    Route::domain(config('orbynia.admin_domain'))->group($panelRoutes);
}

$publicRoutes = function (): void {
    Route::get('/', [PublicSiteController::class, 'home'])->name('home');
    Route::get('/acceder', [AccessPortalController::class, 'index'])->name('access.form');
    Route::post('/acceder', [AccessPortalController::class, 'resolve'])->middleware('throttle:10,1')->name('access.resolve');
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

    Route::get('/contacto', [CommercialController::class, 'contact'])->name('contact.form');
    Route::post('/contacto', [CommercialController::class, 'storeContact'])->middleware('throttle:5,60')->name('contact.store');
    Route::get('/solicitar-evaluacion', [CommercialController::class, 'evaluation'])->name('evaluation.form');
    Route::post('/solicitar-evaluacion', [CommercialController::class, 'storeEvaluation'])->middleware('throttle:3,60')->name('evaluation.store');
    Route::get('/subdominio-disponible/{slug}', [CommercialController::class, 'slugAvailability'])
        ->middleware('throttle:30,1')->name('evaluation.slug');
    Route::get('/confirmar-evaluacion/{uuid}', [CommercialController::class, 'verifyEvaluation'])
        ->middleware('signed')->name('evaluation.verify');
};

if (app()->environment('local', 'testing')) {
    Route::group([], $publicRoutes);
} else {
    Route::domain(config('orbynia.domain'))->group($publicRoutes);
}
