<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('public_complaints', function (Blueprint $table) {
            $table->string('guardian_address', 300)->nullable();
            $table->string('guardian_phone', 32)->nullable();
            $table->string('guardian_email', 190)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('public_complaints', function (Blueprint $table) {
            $table->dropColumn(['guardian_address', 'guardian_phone', 'guardian_email']);
        });
    }
};
