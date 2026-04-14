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
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('classe'); 
            $table->uuid('student')->nullable();
            $table->uuid('tag')->nullable();
            $table->string('name')->nullable(); // Name of the document
            $table->string('path')->nullable();
            $table->string('type')->nullable(); // e.g., 'pdf', 'docx',

            $table->foreign('classe')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('student')->references('id')->on('students')->onDelete('set null');
            $table->foreign('tag')->references('id')->on('tags')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
