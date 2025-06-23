<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('histories_id')->constrained()->onDelete('cascade');
            $table->string('competition_name');
            $table->enum('competition_type', ['individual', 'group']);
            $table->string('winner_name');
            $table->string('winner_school');
            $table->integer('rank');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_histories');
    }
};
