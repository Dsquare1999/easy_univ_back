<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('notifs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user')->nullable(); // qui a déclenché l'action
            $table->string('type');     // ex: student_created, class_updated...
            $table->string('title');    // titre court
            $table->text('message');    // description lisible
            $table->boolean('is_read')->default(false);

            $table->foreign('user')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->index('type');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifs');
    }
};
