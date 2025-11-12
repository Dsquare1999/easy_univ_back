<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('curriculum_vitae')->nullable();
            $table->string('diplomes')->nullable();
            $table->string('autorisation_enseigner')->nullable();
            $table->string('preuve_experience')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};