<?php

namespace App\Http\Livewire;

use App\Models\Flight;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Registration;

class Flights extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $flight_no, $registration, $origin, $destination, $scheduled_time_arrival, $scheduled_time_departure, $flight_type, $keyWord, $flight_id, $selectedFlightId;

    protected $rules = [
        'flight_no' => 'required|string',
        'registration' => 'required|string',
        'origin' => 'required|string',
        'destination' => 'required|string',
        'scheduled_time_arrival' => 'required|date',
        'scheduled_time_departure' => 'required|date',
        'flight_type' => 'required|in:arrival,departure',
    ];

    public function cancel()
    {
        $this->reset();
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

        $this->reset();
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

    public function viewFlight($flight_id)
    {
        $this->selectedFlightId = $flight_id;
    }

    public function getSelectedFlightProperty()
    {
        return $this->selectedFlightId ? Flight::findOrFail($this->selectedFlightId) : null;
    }

    public function render()
    {
        $registrations = Registration::all();
        $keyWord = '%'. $this->keyWord .'%';
        $flights = Flight::latest()
                    ->orWhere('flight_no', 'LIKE', $keyWord)
                    ->orWhere('registration', 'LIKE', $keyWord)
                    ->orderBy('scheduled_time_arrival', 'asc')
                    ->paginate(10);
        return view('livewire.flights.view', [
            'flights' => $flights,
            'registrations' => $registrations,
            'selectedFlight' => $this->selectedFlightId ? Flight::findOrFail($this->selectedFlightId) : null,
        ]);
    }
}