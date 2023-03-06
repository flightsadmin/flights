<?php

namespace Database\Seeders;

use App\Models\Airline;
use App\Models\Registration;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class FlightsDatabaseSeeder extends Seeder
{
    public function run()
    {
        $aircraft_types = [
            'Airbus A320',
            'Airbus A330',
            'Airbus A350',
            'Airbus A380',
            'Boeing 737',
            'Boeing 777',
            'Boeing 747',
            'Boeing 787',
            'Embraer E190',
            'Embraer E145',
            'Gulfstream G550',
        ];

        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            $city = new Airline;
            $city->name = $faker->company;
            $city->iata_code = $faker->unique()->regexify('[A-Z]{2}');
            $city->base = $faker->city;
            $city->save();
        }
        
        for ($i = 0; $i < 50; $i++) {
            $aircraft = new Registration;
            $aircraft->registration = $faker->unique()->regexify('[A-Z]{2}-[0-9]{3}');
            $aircraft->aircraft_type = $faker->randomElement($aircraft_types);
            $aircraft->airline_id = $faker->randomElement(['1', '2', '3', '4', '5', '6', '7']);
            $aircraft->save();
        }

    }
}