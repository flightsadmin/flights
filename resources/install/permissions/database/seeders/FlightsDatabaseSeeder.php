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
        $airlines = [
            [ "name" => "Flydubai",         "iata_code" => "FZ",    "base" => "Dubai, United Arab Emirates" ],
            [ "name" => "Air Arabia",       "iata_code" => "G9",    "base" => "Sharjah, United Arab Emirates" ],
            [ "name" => "Oman Air",         "iata_code" => "WY",    "base" => "Muscat, Oman" ],
            [ "name" => "Salamair",         "iata_code" => "OV",    "base" => "Muscat, Oman" ],
            [ "name" => "Qatar Airways",    "iata_code" => "QR",    "base" => "Doha, Qatar" ],
            [ "name" => "Kenya Airways",    "iata_code" => "KQ",    "base" => "Nairobi, Kenya" ],
            [ "name" => "Emirates",         "iata_code" => "EK",    "base" => "Dubai, United Arab Emirates" ],
            [ "name" => "Air India",        "iata_code" => "AI",    "base" => "Bombay, India" ],
            [ "name" => "Indigo  Airlines", "iata_code" => "6E",    "base" => "Hyderabad, India" ],
            [ "name" => "Jambojet",         "iata_code" => "JM",    "base" => "Nairobi, Kenya" ],
        ];

        foreach ($airlines as $airline) {
            Airline::updateOrCreate($airline);
        }

        $airlines = Airline::all();
        // Seed Routes and Email Addresses
        $airports = ['DOH', 'JFK', 'LHR', 'NBO', 'MCT', 'KWI', 'SYD', 'JED', 'DXB', 'SIN'];
        $departureAirport = $airports[array_rand($airports)];
        foreach ($airlines as $airline) {
            for ($i=0; $i < 4; $i++) { 

                $origin = $departureAirport;
                $destination = $airports[array_rand($airports)];
                
                while ($origin === $destination) {
                    $destination = $airports[array_rand($airports)];
                }
            
                $route = Route::updateOrCreate([
                    'airline_id' => $airline->id,
                    'origin' => $origin,
                    'destination' => $destination,
                ]);
            
                $route->emails()->updateOrCreate([
                    'email' => 'flightsapps@gmail.com',
                    'airline_id' => $airline->id,
                ]);
            
                $departureAirport = $destination;
            }
        }
    }
}