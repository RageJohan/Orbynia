<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_applications', function (Blueprint $table): void {
            $table->string('reserved_slug', 40)->nullable()->unique();
        });

        // Las instancias aprobadas conservan prioridad sobre solicitudes anteriores.
        DB::table('company_applications')->whereNotNull('approved_slug')->orderBy('id')
            ->get(['id', 'approved_slug'])
            ->each(function ($application): void {
                DB::table('company_applications')->where('id', $application->id)
                    ->update(['reserved_slug' => $application->approved_slug]);
            });

        // Al actualizar una instalación existente, la primera solicitud verificada
        // conserva el nombre. Las demás podrán escoger uno nuevo.
        DB::table('company_applications')
            ->where('status', 'pending_review')
            ->whereNotNull('email_verified_at')
            ->orderBy('email_verified_at')
            ->orderBy('id')
            ->get(['id', 'requested_slug'])
            ->each(function ($application): void {
                $taken = DB::table('company_applications')
                    ->where('id', '!=', $application->id)
                    ->where(function ($query) use ($application): void {
                        $query->where('reserved_slug', $application->requested_slug)
                            ->orWhere('approved_slug', $application->requested_slug);
                    })
                    ->exists();

                if ($taken) {
                    DB::table('company_applications')->where('id', $application->id)
                        ->update(['status' => 'slug_conflict', 'updated_at' => now()]);
                    DB::table('company_application_events')->insert([
                        'application_id' => $application->id,
                        'event' => 'slug_conflict',
                        'details' => 'El subdominio solicitado ya estaba asignado al migrar las reservas.',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    return;
                }

                DB::table('company_applications')->where('id', $application->id)
                    ->update(['reserved_slug' => $application->requested_slug]);
            });
    }

    public function down(): void
    {
        Schema::table('company_applications', function (Blueprint $table): void {
            $table->dropUnique(['reserved_slug']);
            $table->dropColumn('reserved_slug');
        });
    }
};
