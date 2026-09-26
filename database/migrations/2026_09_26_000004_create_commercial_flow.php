<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_minka_operator')->default(false);
        });

        Schema::create('commercial_leads', function (Blueprint $table): void {
            $table->id();
            $table->string('source', 32);
            $table->string('company_name', 160);
            $table->string('contact_name', 160);
            $table->string('email', 190);
            $table->string('phone', 32);
            $table->text('message')->nullable();
            $table->string('status', 24)->default('new');
            $table->timestamps();
        });

        Schema::create('company_applications', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('company_name', 160);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 190);
            $table->string('phone', 32);
            $table->string('requested_slug', 40);
            $table->string('approved_slug', 40)->nullable()->unique();
            $table->string('plan_interest', 24);
            $table->string('status', 32)->default('awaiting_verification');
            $table->timestamp('email_verified_at')->nullable();
            $table->date('evaluation_ends_at')->nullable();
            $table->json('enabled_modules')->nullable();
            $table->text('operator_notes')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        Schema::create('company_application_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('application_id')->constrained('company_applications')->cascadeOnDelete();
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event', 40);
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_application_events');
        Schema::dropIfExists('company_applications');
        Schema::dropIfExists('commercial_leads');
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('is_minka_operator');
        });
    }
};
