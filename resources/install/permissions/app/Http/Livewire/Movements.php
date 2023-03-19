<?php

namespace App\Http\Livewire;

use App\Models\Flight;
use App\Models\Movement;
use Livewire\Component;

class Movements extends Component
{
    public $touchdown, $onblocks, $offblocks, $airborne, $passengers, $remarks, $flight_id;

    public function render()
    {
        $flights = Flight::all();
        return view('livewire.movements.view', compact('flights'));
    }

    public function saveMovements()
    {
        $validatedData = $this->validate(
            [
                'touchdown' => 'nullable|date',
                'onblocks' => 'nullable|date',
                'offblocks' => 'nullable|date',
                'airborne' => 'nullable|date',
                'passengers' => 'nullable|integer|min:0',
                'remarks' => 'nullable|string',
                'flight_id' => 'required|exists:flights,id',
            ]
        );

        Movement::create($validatedData);

        $this->dispatchBrowserEvent('closeModal');
        session()->flash('message', 'Movement created successfully.');
        $this->reset(['touchdown','onblocks','offblocks','airborne','passengers','remarks','flight_id']);
    }
}