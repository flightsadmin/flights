<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\Flight;
use App\Models\Airline;
use App\Models\Service;
use App\Models\Registration;
use Livewire\Component;
use Livewire\WithPagination;

class Flights extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $registrations = [], $selectedAirline, $flight_no, $registration, $origin, $destination, $scheduled_time_arrival, $scheduled_time_departure, $flight_type, $keyWord, $flight_id, $selectedDate;
    public $ServiceTypes = [], $flightFields = [], $serviceList = ["Pax Bus", "Crew Bus", "Pushback", "Cleaning", "Lavatory Service", "Passenger Steps"];

    protected $listeners = ['refreshItems' => '$refresh'];

    protected $rules = [
        'selectedAirline'           => 'required|string',
        'flight_no'                 => 'required|string',
        'registration'              => 'required|string',
        'origin'                    => 'required|string',
        'destination'               => 'required|string',
        'scheduled_time_arrival'    => 'required|date',
        'scheduled_time_departure'  => 'required|date',
        'flight_type'               => 'required|in:arrival,departure',
    ];

    public function render()
    {
        $keyWord = '%'. $this->keyWord .'%';
        $flights = Flight::with('service')
                        ->whereDate('scheduled_time_departure', $this->selectedDate)
                        ->where('flight_no', 'LIKE', $keyWord)
                        ->orderBy('scheduled_time_departure', 'asc')
                        ->paginate(50);
        return view('livewire.flights.view', [
            'airlines' => Airline::all(),
            'flights' => $flights,
            'selectedFlight' => $this->flight_id ? Flight::findOrFail($this->flight_id) : null,
        ]);
    }

    public function mount()
    {
        $this->selectedDate = Carbon::now()->format('Y-m-d');
    }
    
    public function cancel()
    {
        $this->resetErrorBag();
        $this->reset(['flightFields', 'ServiceTypes', 'serviceList']);
    }

    public function updatedselectedAirline($airline)
    {
        $this->registrations = Registration::where('airline_id', $airline)->get();
    }

    public function store()
    {
        $this->validate();
        Flight::updateOrCreate(['id' => $this->flight_id], [
            'flight_no' => $this->flight_no,
            'registration' => $this->registration,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'scheduled_time_arrival' => $this->scheduled_time_arrival,
            'scheduled_time_departure' => $this->scheduled_time_departure,
            'flight_type' => $this->flight_type,
        ]);

        $this->reset(['flight_no', 'registration', 'origin', 'destination', 'scheduled_time_arrival', 'scheduled_time_departure', 'flight_type']);
        $this->dispatchBrowserEvent('closeModal');
        session()->flash('message', $this->flight_id ? 'Flight Updated Successfully.' : 'Flight Created Successfully.');
    }

    public function edit($id)
    {
        $flight = Flight::findOrFail($id);
        $this->flight_id = $id;
        $this->flight_no = $flight->flight_no;
        $this->registration = $flight->registration;
        $this->origin = $flight->origin;
        $this->destination = $flight->destination;
        $this->scheduled_time_arrival = $flight->scheduled_time_arrival;
        $this->scheduled_time_departure = $flight->scheduled_time_departure;
        $this->flight_type = $flight->flight_type;
    }

    public function destroy($id)
    {
        Flight::find($id)->delete();
        session()->flash('message', 'Flight Deleted Successfully.');
    }

    public function viewFlight($id)
    {
        $this->flight_id = $id;
    }

    public function getSelectedFlightProperty()
    {
        return $this->flight_id ? Flight::findOrFail($this->flight_id) : null;
    }

    public function addService()
    {
        $this->ServiceTypes[] = 'Service '.rand(100, 999);
        $this->emit('refreshItems');
    }

    public function removeService($index)
    {
        unset($this->ServiceTypes[$index]);
        $this->ServiceTypes = array_values($this->ServiceTypes);
        $this->emit('refreshItems');
    }

    public function createServices()
    {
        foreach ($this->flightFields as $flight) {
            Service::updateOrCreate(['flight_id' => $this->flight_id, 'service_type' => $flight['service_type']], [
                'service_type' => $flight['service_type'],
                'start' => $flight['start'],
                'finish' => $flight['finish'],
                'flight_id' => $this->flight_id,
            ]);
        }

        $this->dispatchBrowserEvent('closeModal');
        session()->flash('message', 'Service Added Successfully.');

        $this->reset(['flightFields', 'ServiceTypes', 'serviceList']);
    }

    public function destroyService($flight)
    {
        Service::where([['flight_id', $this->flight_id], ['service_type', $flight]])->delete();
        session()->flash('message', 'Service Deleted Successfully.');
    }
}
