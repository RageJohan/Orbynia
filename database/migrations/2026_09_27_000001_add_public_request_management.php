<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('public_complaints', function (Blueprint $table): void {
            $table->string('status', 24)->default('received')->index();
            $table->foreignId('responsible_operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->timestamp('response_sent_at')->nullable();
        });

        Schema::table('account_deletion_requests', function (Blueprint $table): void {
            $table->foreignId('responsible_operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->text('response')->nullable();
            $table->timestamp('response_sent_at')->nullable();
        });

        Schema::create('public_request_events', function (Blueprint $table): void {
            $table->id();
            $table->string('request_type', 16);
            $table->unsignedBigInteger('request_id');
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event', 40);
            $table->text('details')->nullable();
            $table->timestamps();
            $table->index(['request_type', 'request_id', 'created_at'], 'public_request_events_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_request_events');

        Schema::table('account_deletion_requests', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('responsible_operator_id');
            $table->dropColumn(['internal_notes', 'response', 'response_sent_at']);
        });

        Schema::table('public_complaints', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('responsible_operator_id');
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'internal_notes', 'response_sent_at']);
        });
    }
};
