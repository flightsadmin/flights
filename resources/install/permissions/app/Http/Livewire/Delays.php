<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\Delay;
use App\Models\Airline;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;


class Delays extends Component
{ 
    use WithPagination, WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    public $numeric_code, $alpha_numeric_code, $description, $accountable, $airline_id, $keyWord, $file;

    public function render()
    {
        $keyWord = '%'. $this->keyWord .'%';
        $delays = Delay::with('airline')
                    ->orWhere('airline_id', 'LIKE', $keyWord)
                    ->orderBy('numeric_code', 'asc')
                    ->paginate();
        return view('livewire.delays.view', [
            "delays" => $delays,
            "airlines" => Airline::all(),
        ]);
    }
    
    public function saveDelay()
    {
        $validatedData = $this->validate([
            'numeric_code'          => 'required',
            'alpha_numeric_code'    => 'required|max:3',
            'description'           => 'required',
            'accountable'           => 'nullable',
            'airline_id'            => 'required',
        ]);

        Delay::create($validatedData);

        $this->dispatchBrowserEvent('closeModal');
        session()->flash('message', 'Delay created successfully.');
        $this->reset();
    }

    public function destroy($id)
    {
        Delay::find($id)->delete();
        session()->flash('message', 'Delay Deleted Successfully.');
    }

    public function downloadDelays()
    {
        $filename = 'delays.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];
    
        $responsible = ['Ground Handling Company', 'Airport', 'Airline', 'Police', 'Immigration'];
    
        $callback = function () use ($responsible) {
            $file = fopen('php://output', 'w');
    
            fputcsv($file, ['numeric_code', 'alpha_numeric_code', 'description', 'accountable', 'airline_id']);
            for ($j = 0; $j < 10; $j++) {
                for ($i = 0; $i < 99; $i++) {
                    $numericCode        = str_pad($i+1, 2, '0', STR_PAD_LEFT);
                    $alphaNumericCode   = $numericCode. chr(rand(65, 90));
                    $description        = strtoupper(substr(str_shuffle(str_repeat($x='abcdefghijklmnopqrstuvwxyz', ceil( 20/strlen($x)))), 1, 70));
                    $accountable        = $responsible[array_rand($responsible)];
                    $airlineId          = rand(1, 10);
                
                    fputcsv($file, [$numericCode, $alphaNumericCode, $description, $accountable, $airlineId]);
                }
            }
            fclose($file);
        };
    
        return new StreamedResponse($callback, 200, $headers);
    }

    public function importDelays()
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

            $delay = new Delay;
            $delay->numeric_code          = $row[0];
            $delay->alpha_numeric_code    = $row[1];
            $delay->description           = $row[2];
            $delay->accountable           = $row[3];
            $delay->airline_id            = $row[4];
            $delay->save();
        }

        session()->flash('message', 'Delays Imported Successfully.');
        return redirect('/delays');
    }
}