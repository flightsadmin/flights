<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\Flight;
use App\Models\Service;
use Livewire\Component;

class Services extends Component
{
    public $ServiceTypes = [], $flightFields = [], $serviceList = ["Cleaning", "Bus", "Pushback", "Toilet Service", "Passenger Steps"];

    public function render()
    {
        return view('livewire.services.view');
    }

    public function addService()
    {
        $this->ServiceTypes[] = 'Service '.rand(100, 999);
    }

    public function removeService($index)
    {
        unset($this->ServiceTypes[$index]);
        $this->ServiceTypes = array_values($this->ServiceTypes);
    }

    public function createServices()
    {
        foreach ($this->flightFields as $flight) {
            $newFlight = new Service();
            $newFlight->service_type = $flight['service_type'];
            $newFlight->start = $flight['start'];
            $newFlight->finish = $flight['finish'];
            $newFlight->flight_id = rand(1, 3);
            $newFlight->save();
        }

        session()->flash('message', 'Service Added Successfully.');
        return redirect('/services');

        $this->reset(['flightFields', 'ServiceTypes', 'serviceList']);
    }
}