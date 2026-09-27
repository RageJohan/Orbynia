<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commercial_leads', function (Blueprint $table): void {
            $table->foreignId('sales_owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('next_follow_up_at')->nullable()->index();
        });

        Schema::table('company_applications', function (Blueprint $table): void {
            $table->foreignId('sales_owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sales_stage', 32)->default('new');
            $table->timestamp('next_follow_up_at')->nullable()->index();
            $table->string('verification_mail_status', 16)->default('pending');
            $table->timestamp('verification_last_attempt_at')->nullable();
            $table->timestamp('verification_sent_at')->nullable();
            $table->unsignedInteger('verification_attempts')->default(0);
        });

        Schema::create('commercial_activities', function (Blueprint $table): void {
            $table->id();
            $table->string('subject_type', 16);
            $table->unsignedBigInteger('subject_id');
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('activity_type', 24);
            $table->text('description');
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->index(['subject_type', 'subject_id', 'occurred_at'], 'commercial_activities_subject');
        });

        Schema::create('commercial_proposals', function (Blueprint $table): void {
            $table->id();
            $table->string('subject_type', 16);
            $table->unsignedBigInteger('subject_id');
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 160);
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('PEN');
            $table->string('billing_period', 16);
            $table->text('modules')->nullable();
            $table->text('scope')->nullable();
            $table->string('status', 16)->default('draft');
            $table->timestamp('sent_at')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();
            $table->index(['subject_type', 'subject_id', 'created_at'], 'commercial_proposals_subject');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commercial_proposals');
        Schema::dropIfExists('commercial_activities');

        Schema::table('company_applications', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('sales_owner_id');
            $table->dropIndex(['next_follow_up_at']);
            $table->dropColumn([
                'sales_stage', 'next_follow_up_at', 'verification_mail_status',
                'verification_last_attempt_at', 'verification_sent_at', 'verification_attempts',
            ]);
        });

        Schema::table('commercial_leads', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('sales_owner_id');
            $table->dropIndex(['next_follow_up_at']);
            $table->dropColumn('next_follow_up_at');
        });
    }
};
