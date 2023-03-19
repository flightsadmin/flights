<?php

namespace Database\Seeders;

use App\Models\Airline;
use App\Models\Route;
use App\Models\Address;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class FlightsDatabaseSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        // Seed Airline Names
        for ($i = 0; $i < 10; $i++) {
            $city = new Airline;
            $city->name = $faker->company;
            $city->iata_code = $faker->unique()->regexify('[A-Z]{2}');
            $city->base = $faker->city;
            $city->save();
        }

        $airlines = Airline::all();
        // Seed Routes and Email Addresses
        $airports = ['DOH', 'JFK', 'LHR', 'NBO', 'MCT', 'KWI', 'SYD', 'JED', 'DXB', 'SIN'];
        foreach ($airlines as $airline) {
                $origin = $airports[array_rand($airports)];
                $destination = $airports[array_rand($airports)];
                
                while ($origin === $destination) {
                    $destination = $airports[array_rand($airports)];
                }
            
            for ($i = 0; $i < 5; $i++) {
                $route = Route::updateOrCreate([
                    'airline_id' => $airline->id,
                    'origin' => $origin,
                    'destination' => $destination,
                ]);

                $route->emails()->updateOrCreate([
                    'email' => $faker->unique()->safeEmail(),
                    'airline_id' => $airline->id,
                ]);
            }
        }
    }
}