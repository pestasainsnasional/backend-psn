<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('school_name');
            $table->string('school_email');
            $table->string('npsn');
            $table->string('companion_teacher_name');
            $table->string('companion_teacher_contact');
            $table->string('companion_teacher_nip');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
