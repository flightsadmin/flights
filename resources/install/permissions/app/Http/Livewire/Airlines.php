<?php

namespace App\Http\Livewire;

use App\Models\Airline;
use Livewire\Component;
use Livewire\WithPagination;

class Airlines extends Component
{ 
    use WithPagination; 
    public $name, $iata_code, $base, $keyWord;

    public function saveAirline()
    {
        $this->validate([
            'name' => 'required',
            'iata_code' => 'required|unique:airlines|max:2',
            'base' => 'required',
        ]);

        Airline::create([
            'name' => $this->name,
            'iata_code' => $this->iata_code,
            'base' => $this->base,
        ]);

        $this->dispatchBrowserEvent('closeModal');
        session()->flash('success', 'Airline created successfully.');
        $this->reset();
    }

    public function destroy($id)
    {
        Airline::find($id)->delete();
        session()->flash('message', 'Airline Deleted Successfully.');
    }

    public function render()
    {
        $keyWord = '%'. $this->keyWord .'%';
        $airlines = Airline::latest()
                    ->orWhere('name', 'LIKE', $keyWord)
                    ->orWhere('iata_code', 'LIKE', $keyWord)
                    ->paginate(10);
        return view('livewire.airlines.view', [
            'airlines' => $airlines
        ]);
    }
}