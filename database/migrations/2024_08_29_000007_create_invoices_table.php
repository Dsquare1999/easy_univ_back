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
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('number')->unique();
            $table->uuid('tag')->nullable();  
            $table->uuid('classe')->nullable();
            $table->uuid('user');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('remain', 10, 2);
            $table->decimal('total', 10, 2);
            $table->decimal('fee', 10, 2);
            $table->string('file');

            $table->foreign('user')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->foreign('tag')->references('id')->on('tags')->onDelete('set null');
            $table->foreign('classe')->references('id')->on('classes')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
