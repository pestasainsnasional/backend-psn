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
            $table->foreignId('teams_id')->constrained()->onDelete('cascade');
            $table->foreignId('participants_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['leader', 'member'])->default('leader');
            $table->timestamps();
            $table->unique(['teams_id', 'participants_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
