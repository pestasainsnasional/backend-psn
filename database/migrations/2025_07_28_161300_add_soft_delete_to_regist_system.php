<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('competitions', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('competition_types', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

         Schema::table('team_members', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('competitions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('competition_types', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
  
    }
};
