<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('competition_type_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->text('rules');
            $table->string('major');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
