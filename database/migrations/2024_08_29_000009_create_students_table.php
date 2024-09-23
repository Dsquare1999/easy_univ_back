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
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('classe');
            $table->uuid('tag')->nullable();
            $table->enum('titre', ['BRS', 'ATP', 'SPR'])->default('ATP');
            $table->enum('statut', ['EN ATTENTE', 'PRE-INSCRIT', 'REFUSE', 'INSCRIT'])->default('EN ATTENTE');
            $table->string('file')->nullable();

            $table->foreignId('user')->constrained()->onDelete('cascade');
            $table->foreign('classe')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('tag')->references('id')->on('tags')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classe_user');
    }
};
