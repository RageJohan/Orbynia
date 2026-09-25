<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_complaints', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 24)->nullable()->unique();
            $table->string('receipt_token', 64)->unique();
            $table->string('first_name', 120);
            $table->string('last_name', 120);
            $table->string('document_type', 12);
            $table->string('document_number', 24);
            $table->string('email', 190);
            $table->string('phone', 32)->nullable();
            $table->string('address', 300);
            $table->string('item_type', 12);
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('complaint_type', 12);
            $table->text('description');
            $table->text('requested_resolution');
            $table->timestamp('receipt_emailed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('account_deletion_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 24)->nullable()->unique();
            $table->string('company', 120);
            $table->string('full_name', 180);
            $table->string('email', 190);
            $table->string('account_identifier', 190);
            $table->text('details')->nullable();
            $table->string('status', 24)->default('received');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_deletion_requests');
        Schema::dropIfExists('public_complaints');
    }
};