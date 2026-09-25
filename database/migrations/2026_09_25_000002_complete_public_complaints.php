<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('public_complaints', function (Blueprint $table) {
            $table->boolean('is_minor')->default(false);
            $table->string('guardian_name', 180)->nullable();
            $table->string('guardian_document', 40)->nullable();
            $table->text('item_description')->nullable();
            $table->text('provider_actions')->nullable();
            $table->timestamp('responded_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('public_complaints', function (Blueprint $table) {
            $table->dropColumn([
                'is_minor', 'guardian_name', 'guardian_document',
                'item_description', 'provider_actions', 'responded_at',
            ]);
        });
    }
};
