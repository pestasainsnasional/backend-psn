<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_timelines', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('competition_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->integer('order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_timelines');
    }
};
