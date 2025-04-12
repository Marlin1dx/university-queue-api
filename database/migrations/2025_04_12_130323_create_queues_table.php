<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
{
    Schema::create('queues', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Пример: "Приемная комиссия факультета X"
        $table->integer('current_position')->default(0);
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
