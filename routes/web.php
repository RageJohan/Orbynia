<?php

use App\Http\Controllers\CommercialController;
use App\Http\Controllers\AccessPortalController;
use App\Http\Controllers\MinkaPanelController;
use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\SalesTrackingController;
use Illuminate\Support\Facades\Route;

$panelRoutes = function (): void {
    Route::get('/login', [MinkaPanelController::class, 'loginForm'])->name('minka.login')->middleware('guest');
    Route::post('/login', [MinkaPanelController::class, 'login'])->name('minka.login.store')->middleware('throttle:5,1');
    Route::middleware('auth')->group(function (): void {
        Route::get('/', [MinkaPanelController::class, 'dashboard'])->name('minka.dashboard');
        Route::get('/comercial', [MinkaPanelController::class, 'commercial'])->name('minka.commercial');
        Route::get('/atencion-legal', [MinkaPanelController::class, 'legal'])->name('minka.legal');
        Route::get('/solicitudes/{id}', [MinkaPanelController::class, 'show'])->name('minka.application');
        Route::get('/seguimiento/{type}/{id}', [SalesTrackingController::class, 'show'])
            ->whereIn('type', ['contacto', 'solicitud'])->name('minka.sales.show');
        Route::post('/seguimiento/{type}/{id}', [SalesTrackingController::class, 'update'])
            ->whereIn('type', ['contacto', 'solicitud'])->name('minka.sales.update');
        Route::post('/seguimiento/{type}/{id}/actividades', [SalesTrackingController::class, 'addActivity'])
            ->whereIn('type', ['contacto', 'solicitud'])->name('minka.sales.activity');
        Route::post('/seguimiento/{type}/{id}/propuestas', [SalesTrackingController::class, 'addProposal'])
            ->whereIn('type', ['contacto', 'solicitud'])->name('minka.sales.proposal');
        Route::post('/seguimiento/{type}/{id}/propuestas/{proposalId}', [SalesTrackingController::class, 'updateProposal'])
            ->whereIn('type', ['contacto', 'solicitud'])->name('minka.sales.proposal.update');
        Route::get('/reclamaciones/{id}', [MinkaPanelController::class, 'publicCase'])
            ->defaults('type', 'complaint')->name('minka.complaint');
        Route::post('/reclamaciones/{id}', [MinkaPanelController::class, 'updatePublicCase'])
            ->defaults('type', 'complaint')->name('minka.complaint.update');
        Route::get('/eliminaciones/{id}', [MinkaPanelController::class, 'publicCase'])
            ->defaults('type', 'deletion')->name('minka.deletion');
        Route::post('/eliminaciones/{id}', [MinkaPanelController::class, 'updatePublicCase'])
            ->defaults('type', 'deletion')->name('minka.deletion.update');
        Route::post('/solicitudes/{id}/aprobar', [MinkaPanelController::class, 'approve'])->name('minka.application.approve');
        Route::post('/solicitudes/{id}/activar', [MinkaPanelController::class, 'activate'])->name('minka.application.activate');
        Route::post('/solicitudes/{id}/rechazar', [MinkaPanelController::class, 'reject'])->name('minka.application.reject');
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
    Route::get('/solicitar-evaluacion/{uuid}/correo', [CommercialController::class, 'verificationStatus'])
        ->name('evaluation.verification-status');
    Route::post('/solicitar-evaluacion/{uuid}/reenviar', [CommercialController::class, 'resendVerification'])
        ->middleware('throttle:3,60')->name('evaluation.verification-resend');
    Route::get('/reenviar-confirmacion', [CommercialController::class, 'resendForm'])->name('evaluation.resend-form');
    Route::post('/reenviar-confirmacion', [CommercialController::class, 'resendByEmail'])
        ->middleware('throttle:3,60')->name('evaluation.resend-email');
    Route::get('/subdominio-disponible/{slug}', [CommercialController::class, 'slugAvailability'])
        ->middleware('throttle:30,1')->name('evaluation.slug');
    Route::get('/confirmar-evaluacion/{uuid}', [CommercialController::class, 'verifyEvaluation'])
        ->middleware('signed')->name('evaluation.verify');
    Route::post('/confirmar-evaluacion/{uuid}/subdominio', [CommercialController::class, 'changeRequestedSlug'])
        ->middleware(['signed', 'throttle:5,1'])->name('evaluation.slug.change');
    Route::get('/evaluacion-confirmada', [CommercialController::class, 'confirmed'])->name('evaluation.confirmed');
};

if (app()->environment('local', 'testing')) {
    Route::group([], $publicRoutes);
} else {
    Route::domain(config('orbynia.domain'))->group($publicRoutes);
}
