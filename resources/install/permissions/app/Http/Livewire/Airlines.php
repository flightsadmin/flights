<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
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
        Airline::find($id)->delete();
        session()->flash('message', 'Airline Deleted Successfully.');
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