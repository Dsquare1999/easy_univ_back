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
        Schema::create('classes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('filiere'); 
            $table->uuid('cycle'); 
            $table->integer('year');
            $table->integer('academic_year');
            $table->enum('parts', ['SEM', 'TRI']);
            $table->integer('status')->default(0);
            
            $table->foreign('cycle')->references('id')->on('cycles')->onDelete('cascade');
            $table->foreign('filiere')->references('id')->on('filieres')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
