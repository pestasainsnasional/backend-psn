<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUlid('participant_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignUlid('competition_id')->constrained()->onDelete('cascade');
            $table->foreignUlid('team_id')->constrained()->onDelete('cascade');
            $table->string('payment_unique_code', 7)->nullable()->after('status');
            $table->timestamp('payment_code_expires_at')->nullable()->after('payment_unique_code');
            $table->enum('status', ['draft_step_1','draft_step_2','draft_step_3','draft_step_4', 'pending', 'verified', 'rejected'])->default('draft_step_1');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
