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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->string('room_type');
            $table->decimal('price_per_night', 10, 2);
            $table->boolean('availability')->default(true);
            $table->string('image')->nullable();
            $table->string('room_view')->nullable();
            $table->string('pool_type')->nullable();
            $table->integer('room_stars')->default(1);
            $table->boolean('has_parking')->default(false);
            $table->boolean('has_airport_transfer')->default(false);
            $table->boolean('has_wifi')->default(true);
            $table->boolean('has_coffee_maker')->default(false);
            $table->boolean('has_bar')->default(false);
            $table->boolean('has_breakfast')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
