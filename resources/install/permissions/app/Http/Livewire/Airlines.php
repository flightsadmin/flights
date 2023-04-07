<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\Route;
use App\Models\Address;
use App\Models\Airline;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Airlines extends Component
{ 
    use WithPagination, WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    public $name, $iata_code, $base, $airline_id, $keyWord, $file;
    public $origin, $destination, $flight_time, $emails = [], $email;

    public function render()
    {
        $keyWord = '%'. $this->keyWord .'%';
        $airlines = Airline::latest()
                    ->orWhere('name', 'LIKE', $keyWord)
                    ->orWhere('iata_code', 'LIKE', $keyWord)
                    ->paginate();
        return view('livewire.airlines.view', [
            'airlines' => $airlines
        ]);
    }
    
    public function saveAirline()
    {
        $validatedData = $this->validate([
            'name' => 'required',
            'iata_code' => 'required|max:2|unique:airlines,id,'. $this->airline_id,
            'base' => 'required',
        ]);

        Airline::updateOrCreate(['id' => $this->airline_id], $validatedData);

        $this->dispatchBrowserEvent('closeModal');
        session()->flash('message', 'Airline created successfully.');
        $this->reset();
    }

    public function edit($id)
    {
        $airline = Airline::findOrFail($id);
        $this->airline_id = $id;
        $this->name = $airline->name;
        $this->iata_code = $airline->iata_code;
        $this->base = $airline->base;
    }

    public function destroy($id)
    {
        Airline::findOrFail($id)->delete();
        session()->flash('message', 'Airline Deleted Successfully.');
    }

    public function addEmail($email)
    {
        $this->validate(['email' => 'required|email']);
        $this->emails[] = strtolower($email);
    }

    public function removeEmail($email)
    {
        $key = array_search($email, $this->emails);

        if ($key !== false) {
            unset($this->emails[$key]);
        }
    }

    public function saveRoute()
    {
        $validatedData = $this->validate([
                'airline_id'    => 'required|exists:airlines,id',
                'origin'        => 'required|string|min:3|max:20',
                'destination'   => 'required|string|min:3|max:20',
            ]);
        $route = Route::updateOrCreate($validatedData);
        $route->flight_time = date("H:i", strtotime(str_pad(trim($this->flight_time), 4, '0', STR_PAD_LEFT))); 
        $route->save();

        foreach ($this->emails as $email) {
            $route->emails()->updateOrCreate([
                'email' => strtolower($email),
                'airline_id' => $validatedData['airline_id'],
            ]);
        }
        $this->dispatchBrowserEvent('closeModal');
        session()->flash('message', $route->wasRecentlyCreated ? 'Route Created successfully.' : 'Route Updated successfully.');
        $this->reset();
    }

    public function editRoute($id)
    {
        $route = Route::findOrFail($id);
        $this->airline_id = $route->airline_id;
        $this->origin = $route->origin;
        $this->destination = $route->destination;
        $this->flight_time = $route->flight_time;
        $this->emails = $route->emails()->pluck('email')->toArray();
    }

    public function deleteRoute($id)
    {
        Address::findOrFail($id)->delete();
        session()->flash('message', 'Route Deleted Successfully.');
    }

    public function downloadAirlines()
    {
        $filename = 'airlines.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];
    
        $cities = ['New York', 'London', 'Tokyo', 'Paris', 'Sydney', 'Los Angeles', 'Moscow', 'Shanghai', 'Dubai', 'Mumbai'];
    
        $callback = function () use ($cities) {
            $file = fopen('php://output', 'w');
    
            fputcsv($file, ['name', 'iata_code', 'base']);
    
            for ($i = 0; $i < 10; $i++) {
                $name = 'Airline ' . str_pad($i+1, 4, '0', STR_PAD_LEFT);
                $iataCode = chr(rand(65, 90)) . chr(rand(65, 90));
                $base = $cities[array_rand($cities)];
    
                fputcsv($file, [$name, $iataCode, $base]);
            }
    
            fclose($file);
        };
    
        return new StreamedResponse($callback, 200, $headers);
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

            $airline = new Airline;
            $airline->name = $row[0];
            $airline->iata_code = $row[1];
            $airline->base = $row[2];
            $airline->save();
        }

        session()->flash('message', 'Airlines Imported Successfully.');
        return redirect('/airlines');
    }
}