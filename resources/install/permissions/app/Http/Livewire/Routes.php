<?php

namespace App\Http\Livewire;

use App\Models\Airline;
use App\Models\Route;
use Livewire\Component;

class Routes extends Component
{
    public $airlineId, $origin, $destination, $emails = [], $email;

    protected $rules = [
        'airlineId'     => 'required|exists:airlines,id',
        'origin'        => 'required|string|max:20',
        'destination'   => 'required|string|max:20'
    ];

    public function addEmail($email)
    {
        $this->validate(['email' => 'required|email']);
        $this->emails[] = $email;
    }

    public function removeEmail($email)
    {
        $key = array_search($email, $this->emails);

        if ($key !== false) {
            unset($this->emails[$key]);
        }
    }

    public function save()
    {
        $validatedData = $this->validate();
        $route = Route::updateOrCreate([
            'airline_id' => $validatedData['airlineId'],
            'origin' => $validatedData['origin'],
            'destination' => $validatedData['destination'],
        ]);
        
        foreach ($this->emails as $email) {
            $route->emails()->updateOrCreate([
                'email' => $email,
                'airline_id' => $validatedData['airlineId'],
            ]);
        }

        session()->flash('success', 'Route created successfully.');
        return redirect('/addresses');
    }

    public function render()
    {
        $airlines = Airline::all();
        return view('livewire.routes.view', compact('airlines'));
    }
}