<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_types', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', ['individu', 'group-2-orang, group-3-orang']);
            $table->string('current_batch');
            $table->integer('slot_remaining')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_types');
    }
};
