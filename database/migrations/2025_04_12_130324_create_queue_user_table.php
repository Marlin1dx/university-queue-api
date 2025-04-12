<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   // database/migrations/[...]_create_queue_user_table.php
    public function up(): void
    {   
    Schema::create('queue_user', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained();
        $table->foreignId('queue_id')->constrained();
        $table->integer('position');
        $table->string('status')->default('waiting'); // waiting, called, completed
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_user');
    }
};
