<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_no');
            $table->string('registration');
            $table->string('origin');
            $table->string('destination');
            $table->dateTime('scheduled_time_arrival');
            $table->dateTime('scheduled_time_departure');
            $table->enum('flight_type', ['domestic', 'international']);
            $table->timestamps();
        });
        
        Schema::create('airlines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('iata_code');
            $table->string('base');
            $table->timestamps();
        });

        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration');
            $table->string('aircraft_type');
            $table->foreignId('airline_id')->constrained('airlines');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('flights');
        Schema::dropIfExists('airlines');
        Schema::dropIfExists('registrations');
    }
};