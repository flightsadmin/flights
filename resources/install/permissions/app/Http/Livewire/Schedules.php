<?php

namespace App\Http\Livewire;

use App\Models\Flight;
use Carbon\Carbon;
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
        return view('livewire.schedules.create');
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
                $flight->flight_no = strtoupper($this->flightFields[$flightNumber]['flight_no']);
                $flight->registration = '';
                $flight->origin = strtoupper($this->flightFields[$flightNumber]['origin'] ?? 'DOH');
                $flight->destination = strtoupper($this->flightFields[$flightNumber]['destination'] ?? 'MCT');
                $flight->scheduled_time_arrival = $date->format('Y-m-d '). $this->flightFields[$flightNumber]['arrival'] ?? '00:00';
                $flight->scheduled_time_departure = $date->format('Y-m-d '). $this->flightFields[$flightNumber]['departure'] ?? '00:00';
                $flight->flight_type = strtoupper('departure');
                $flight->save();
                $date = $date->next($day);
            }
        }
        session()->flash('message', 'Schedule Created Successfully.');
        return redirect('/flights');

        // Clear the selected dates after creating the flights
        $this->selectedDays = [];
        $this->flightNumbers = [];
        $this->flightFields = [];
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
            $flight->flight_no = $row[0];
            $flight->registration = $row[1];
            $flight->origin = $row[2];
            $flight->destination = $row[3];
            $flight->scheduled_time_arrival = date('Y-m-d H:s', strtotime($row[4]));
            $flight->scheduled_time_departure = date('Y-m-d H:s', strtotime($row[5]));
            $flight->flight_type = $row[6];
            $flight->save();
        }

        session()->flash('message', 'Schedule Imported Successfully.');
        return redirect('/flights');
    }


    public function downloadSample()
    {
        $filename = 'sample_flights.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['flight_no', 'registration', 'origin', 'destination', 'scheduled_time_arrival', 'scheduled_time_departure', 'flight_type']);

            fputcsv($file, ['AC121', '4G-LLM', 'DOH', 'MCT','2023-03-10 12:00:00', '2023-03-10 14:30:00', 'departure']);
            fputcsv($file, ['AC122', '4G-LLN', 'DXB', 'DOH','2023-03-10 12:00:00', '2023-03-10 14:30:00', 'departure']);
            fputcsv($file, ['AC123', '4G-LLO', 'ISU', 'DOH','2023-03-10 12:00:00', '2023-03-10 14:30:00', 'departure']);

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
        session()->flash('message', 'Sample File Downloaded Successfully.');
    }
}