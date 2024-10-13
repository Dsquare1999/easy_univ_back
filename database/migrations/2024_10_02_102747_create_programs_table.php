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
        Schema::create('programs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('classe'); 
            $table->uuid('matiere'); 
            $table->uuid('teacher')->nullable();
            
            $table->date('day');
            $table->time('h_begin');
            $table->time('h_end');
            $table->enum('status', ['RATTRAPAGE', 'ANNULE', 'REPORTE', 'EFFECTUE', 'EN ATTENTE'])->default('EN ATTENTE');
            $table->text('observation')->nullable();
            $table->uuid('report')->nullable();

            $table->foreign('classe')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('matiere')->references('id')->on('matieres')->onDelete('cascade');
            $table->foreign('report')->references('id')->on('programs')->onDelete('set null');
            $table->foreign('teacher')->references('id')->on('users')->constrained()->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
