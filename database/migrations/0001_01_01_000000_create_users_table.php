<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('google_id')->nullable()->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
        
    
        Schema::create('drafts', function (Blueprint $table) {
            $table->ulid('id')->primary(); 
            $table->foreignUlid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUlid('competition_id');
            $table->json('data'); 
            $table->timestamps();
            
            $table->unique(['user_id', 'competition_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drafts');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};