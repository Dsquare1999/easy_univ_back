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
        Schema::create('matieres', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->uuid('classe');
            $table->uuid('teacher')->nullable();

            $table->string('name');
            $table->string('code')->unique();
            $table->string('libelle');
            $table->integer('hours');
            $table->integer('coefficient');
            $table->integer('year_part');

            $table->foreign('teacher')->references('id')->on('users')->constrained()->onDelete('set null');
            $table->foreign('classe')->references('id')->on('classes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matieres');
    }
};
