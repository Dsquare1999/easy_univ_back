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
        Schema::create('operations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tag')->nullable(); 
            $table->uuid('invoice'); 
            $table->enum('type', ['in', 'out']);
            $table->decimal('montant', 10, 2);
            $table->date('date');

            $table->foreign('tag')->references('id')->on('tags')->onDelete('set null');
            $table->foreign('invoice')->references('id')->on('invoices')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};
