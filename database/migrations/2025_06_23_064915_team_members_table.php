<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('participant_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['leader', 'member'])->default('leader');
            $table->timestamps();
            $table->unique(['team_id', 'participant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
