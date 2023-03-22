<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\Flight;
use App\Models\Airline;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Schedules extends Component
{
    use WithFileUploads;
    public $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    public $flightNumbers = [], $selectedDays = [], $flightFields = [], $startDate, $endDate, $file;

    public function render()
    {
        $airlines = Airline::all();
        return view('livewire.schedules.view', compact('airlines'));
    }

    public function mount()
    {
        $this->startDate = Carbon::now()->format('Y-m-d');
        $this->endDate = Carbon::now()->addDays(30)->format('Y-m-d');
    }

    public function addFlights()
    {
        $this->flightNumbers[] = rand(100, 999);
    }

    public function removeFlights($index)
    {
        unset($this->flightNumbers[$index]);
        $this->flightNumbers = array_values($this->flightNumbers);
    }

    public function createFlights()
    {
        foreach ($this->selectedDays as $selectedDay) {
            list($flightNumber, $day) = explode('-', $selectedDay);

            // Calculate the first occurrence of the selected day within the date range
            $date = Carbon::parse($this->startDate)->next($day);
            if ($date->lt($this->startDate)) {
                $date = $date->next($day);
            }

            // Create flights for each occurrence of the selected day within the date range
            while ($date->lte($this->endDate)) {
                $flight = new Flight;
                $flight->airline_id = strtoupper($this->flightFields[$flightNumber]['airline_id']);
                $flight->flight_no = strtoupper($this->flightFields[$flightNumber]['flight_no']);
                $flight->registration = '';
                $flight->origin = strtoupper($this->flightFields[$flightNumber]['origin'] ?? 'DOH');
                $flight->destination = strtoupper($this->flightFields[$flightNumber]['destination'] ?? 'MCT');
                $flight->scheduled_time_arrival = $date->format('Y-m-d '). $this->flightFields[$flightNumber]['arrival'] ?? '00:00';
                $flight->scheduled_time_departure = $date->format('Y-m-d '). $this->flightFields[$flightNumber]['departure'] ?? '00:00';
                $flight->flight_type = strtoupper($this->flightFields[$flightNumber]['flight_type'] ?? 'departure');
                $flight->save();
                $date = $date->next($day);
            }
        }
        session()->flash('message', 'Schedule Created Successfully.');
        return redirect('/flights');
        $this->reset(['selectedDays', 'flightNumbers', 'flightFields']);
    }

    public function import()
    {
        // Validate the file upload
        $this->validate([
            'file' => 'nullable|mimes:csv,txt'
        ]);

        // Read the CSV file using the `fgetcsv()` function
        $csvFile = fopen($this->file->getRealPath(), 'r');
        $headerRow = true;
        while (($row = fgetcsv($csvFile)) !== false) {
            // Skip the header row
            if ($headerRow) {
                $headerRow = false;
                continue;
            }

            $flight = new Flight;
            $flight->airline_id = $row[0];
            $flight->flight_no = $row[1];
            $flight->registration = $row[2];
            $flight->origin = $row[3];
            $flight->destination = $row[4];
            $flight->scheduled_time_arrival = date('Y-m-d H:s', strtotime($row[5]));
            $flight->scheduled_time_departure = date('Y-m-d H:s', strtotime($row[6]));
            $flight->flight_type = $row[7];
            $flight->save();
        }

        session()->flash('message', 'Schedule Imported Successfully.');
        return redirect('/flights');
    }

    public function downloadSample()
    {
        $filename = 'flights.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];
    
        $callback = function () {
            $file = fopen('php://output', 'w');
    
            $headers = [
                'airline_id',
                'flight_no',
                'registration',
                'origin',
                'destination',
                'scheduled_time_arrival',
                'scheduled_time_departure',
                'flight_type',
                // 'ground_time'
            ];
    
            fputcsv($file, $headers);
    
            $airports = ['DOH', 'JFK', 'LHR', 'NBO', 'MCT', 'KWI', 'SYD', 'JED', 'DXB', 'SIN'];
            $start_date = Carbon::now();
            $end_date = $start_date->copy()->addDays(30);
    
            while ($start_date <= $end_date) {
                for ($i = 0; $i < 100; $i++) {
                    $airlineId = rand(1, 10);
                    $flightNo = 'XA' . str_pad($i+1, 4, '0', STR_PAD_LEFT);
                    $registration = 'A7-B' . chr(rand(65, 90)) . chr(rand(65, 90));
                    $origin = $airports[array_rand($airports)];
                    $destination = $airports[array_rand($airports)];
                    while ($destination == $origin) {
                        $destination = $airports[array_rand($airports)];
                    }
                    $arrivalTime = $start_date->copy()->addMinutes(rand(0, 1440))->format('Y-m-d H:i:s');
                    $departureTime = date('Y-m-d H:i:s', strtotime($arrivalTime . ' + ' . rand(60, 180) . ' minutes'));
                    $flightType = ($i % 2 == 0) ? 'departure' : 'arrival';
                    // $groundTime = Carbon::parse($arrivalTime)->diff(Carbon::parse($departureTime))->format('%h hours %i minutes');
    
                    fputcsv($file, [
                        $airlineId,
                        $flightNo,
                        $registration,
                        $origin,
                        $destination,
                        $arrivalTime,
                        $departureTime,
                        $flightType,
                        // $groundTime
                    ]);
                }
                $start_date->addDay();
            }
    
            fclose($file);
        };
    
        return new StreamedResponse($callback, 200, $headers);
        session()->flash('message', 'Sample File Downloaded Successfully.');
    }
}