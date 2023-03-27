<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Route;
use App\Models\Flight;
use App\Models\Address;
use App\Models\Airline;
use Faker\Factory as Faker;
use App\Models\Registration;
use Illuminate\Database\Seeder;
use App\Models\AirlineDelayCode;

class FlightsDatabaseSeeder extends Seeder
{
    public function run()
    {
        //Seed Airlines
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

        // Seed Routes and Email Addresses
        $airlines = Airline::all();
        $airports = ['DOH', 'JFK', 'LHR', 'NBO', 'MCT', 'KWI', 'SYD', 'JED', 'DXB', 'SIN'];
        $departureAirport = $airports[array_rand($airports)];
        foreach ($airlines as $airline) {
            for ($i=0; $i < 2; $i++) { 

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
                $route->flight_time = date('H:i', strtotime('+'. rand(2, 3) .' hours'));
                $route->save();
            
                $route->emails()->updateOrCreate([
                    'email' => 'flightsapps@gmail.com',
                    'airline_id' => $airline->id,
                ]);
            
                $departureAirport = $destination;
            }
        }
        
        // Seed Registrations
        $aircraft_types = ['Airbus A320', 'Airbus A330', 'Airbus A350', 'Airbus A380', 'Boeing 737', 'Boeing 777', 'Boeing 747', 'Boeing 787'];
        $used_registrations = Registration::all()->pluck('registration')->toArray();
        for ($i = 0; $i < 50; $i++) {
            $registration = '';
            do {
                $registration = 'A7B' . chr(rand(65, 90)) . chr(rand(65, 90));
            } while (in_array($registration, $used_registrations));
            $used_registrations[] = $registration;

            $aircraftType = $aircraft_types[array_rand($aircraft_types)];
            $airlineId = rand(1, $airlines->count());

            Registration::updateOrCreate([
                'registration'  => $registration,
                'aircraft_type' => $aircraftType,
                'airline_id'    => $airlineId,
            ]);
        }

        // Seed Flights
        $start_date = Carbon::now();
        $end_date = $start_date->copy()->addDays(30);

        while ($start_date <= $end_date) {
            for ($i = 0; $i < 10; $i++) {
                $airlineId = rand(1, $airlines->count());
                $flightNo = 'XA' . str_pad($i+1, 4, '0', STR_PAD_LEFT);
                $registration =  Registration::where('airline_id', $airlineId)->pluck('registration')->first();
                $origin = $airports[array_rand($airports)];
                $destination = $airports[array_rand($airports)];
                while ($destination == $origin) {
                    $destination = $airports[array_rand($airports)];
                }
                $arrivalTime = $start_date->copy()->addMinutes(rand(0, 1440))->format('Y-m-d H:i:s');
                $departureTime = date('Y-m-d H:i:s', strtotime($arrivalTime . ' + ' . rand(60, 180) . ' minutes'));
                $flightType = ($i % 2 == 0) ? 'departure' : 'arrival';

                Flight::updateOrCreate([
                    'airline_id'                => $airlineId,
                    'flight_no'                 => $flightNo,
                    'registration'              => $registration,
                    'origin'                    => $origin,
                    'destination'               => $destination,
                    'scheduled_time_arrival'    => $arrivalTime,
                    'scheduled_time_departure'  => $departureTime,
                    'flight_type'               => $flightType,
                ]);
            }
            $start_date->addDay();
        }

        // Seed Delay Codes
        $responsible = ['Ground Handling', 'Airport', 'Airline', 'Police', 'Immigration'];
        for ($j = 0; $j < 10; $j++) {
            for ($i = 0; $i < 99; $i++) {
                $numericCode      = str_pad($i+1, 2, '0', STR_PAD_LEFT);
                $alphaNumericCode = $numericCode. chr(rand(65, 90));
                $description      = strtoupper(substr(str_shuffle(str_repeat($x='abcdefghijklmnopqrstuvwxyz', ceil( 20/strlen($x)))), 1, 70));
                $accountable      = $responsible[array_rand($responsible)];
                $airlineId        = rand(1, $airlines->count());
            
                AirlineDelayCode::updateOrCreate([
                    'numeric_code'          => $numericCode,
                    'alpha_numeric_code'    => $alphaNumericCode,
                    'description'           => $description,
                    'accountable'           => $accountable,
                    'airline_id'            => $airlineId,
                ]);
            }
        }
    }
}