<?php

namespace Database\Seeders;

use App\Models\Airline;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class FlightsDatabaseSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            $city = new Airline;
            $city->name = $faker->company;
            $city->iata_code = $faker->unique()->regexify('[A-Z]{2}');
            $city->base = $faker->city;
            $city->save();
        }

        // for ($i = 0; $i < 100; $i++) {
        //     $service = new Service;
        //     $service->service_type = $faker->word;
        //     $service->start = now();
        //     $service->finish = now();
        //     $service->flight_id = rand(1, 10);
        //     $service->save();
        // }
    }
}