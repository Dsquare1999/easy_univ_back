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
        Schema::create('releves', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('classe'); 
            $table->uuid('matiere'); 
            $table->uuid('student')->nullable(); 
            $table->decimal('exam1', 10, 2)->nullable();
            $table->text('observation_exam1')->nullable();
            $table->decimal('exam2', 10, 2)->nullable();
            $table->text('observation_exam2')->nullable();

            $table->decimal('partial', 10, 2)->nullable();
            $table->text('observation_partial')->nullable();

            $table->decimal('remedial', 10, 2)->nullable();
            $table->text('observation_remedial')->nullable();

            $table->enum('status', ['RATTRAPAGE', 'REPRISE', 'VALIDE', 'EN ATTENTE'])->default('EN ATTENTE');
            
            $table->foreign('classe')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('matiere')->references('id')->on('matieres')->onDelete('cascade');
            $table->foreign('student')->references('id')->on('students')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releves');
    }
};
