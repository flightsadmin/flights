<?php

namespace App\Http\Livewire;

use App\Models\Airline;
use App\Models\Route;
use Livewire\Component;

class Routes extends Component
{
    public $airline_id, $origin, $destination, $flight_time, $emails = [], $email;

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
        $validatedData = $this->validate([
                'airline_id'    => 'required|exists:airlines,id',
                'origin'        => 'required|string|min:3|max:20',
                'destination'   => 'required|string|min:3|max:20',
            ]);
        $route = Route::updateOrCreate($validatedData);
        $route->flight_time = date("H:i", strtotime($this->flight_time));
        $route->save();

        foreach ($this->emails as $email) {
            $route->emails()->updateOrCreate([
                'email' => $email,
                'airline_id' => $validatedData['airline_id'],
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