<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('airlines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('iata_code');
            $table->string('base');
            $table->timestamps();
        });

        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airline_id')->constrained('airlines')->onDelete('cascade');
            $table->string('flight_no');
            $table->string('registration');
            $table->string('origin');
            $table->string('destination');
            $table->dateTime('scheduled_time_arrival');
            $table->dateTime('scheduled_time_departure');
            $table->enum('flight_type', ['arrival', 'departure']);
            $table->timestamps();
        });
        
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration')->unique();
            $table->string('aircraft_type');
            $table->foreignId('airline_id')->constrained('airlines')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_type');
            $table->dateTime('start');
            $table->dateTime('finish');
            $table->foreignId('flight_id')->constrained('flights')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_id')->constrained('flights')->onDelete('cascade');
            $table->timestamp('offblocks')->nullable();
            $table->timestamp('airborne')->nullable();
            $table->timestamp('touchdown')->nullable();
            $table->timestamp('onblocks')->nullable();
            $table->integer('passengers')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('origin');
            $table->string('destination');
            $table->foreignId('airline_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->foreignId('airline_id')->constrained()->onDelete('cascade');
            $table->foreignId('route_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('airlines');
        Schema::dropIfExists('flights');
        Schema::dropIfExists('registrations');
        Schema::dropIfExists('services');
        Schema::dropIfExists('movements');
        Schema::dropIfExists('routes');
        Schema::dropIfExists('addresses');
    }
};