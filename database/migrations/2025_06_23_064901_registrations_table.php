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
            $table->foreignUlid('participant_id')->constrained()->onDelete('cascade');
            $table->foreignUlid('competition_id')->constrained()->onDelete('cascade');
            $table->foreignUlid('team_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['draft', 'pending', 'verified', 'rejected'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
