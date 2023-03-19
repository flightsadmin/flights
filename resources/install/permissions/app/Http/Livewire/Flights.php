<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\Route;
use App\Models\Flight;
use App\Models\Address;
use App\Models\Airline;
use App\Models\Service;
use Livewire\Component;
use App\Models\Movement;
use App\Models\Registration;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Mail;

class Flights extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $registrations = [], $airline_id, $flight_no, $registration, $origin, $destination, $scheduled_time_arrival, $scheduled_time_departure, $flight_type, $keyWord, $flight_id, $selectedDate;
    public $ServiceTypes = [], $flightFields = [], $serviceList = ["Pax Bus", "Crew Bus", "Pushback", "Cleaning", "Lavatory Service", "Passenger Steps"];
    public $touchdown, $onblocks, $offblocks, $airborne, $passengers, $remarks;

    protected $listeners = ['refreshItems' => '$refresh'];

    protected $rules = [
        'airline_id'                => 'required',
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
        $flights = Flight::with('service', 'movement')
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
        $this->reset(['flightFields', 'ServiceTypes', 'serviceList', 'flight_id']);
    }

    public function updatedairlineId($airline)
    {
        $this->registrations = Registration::where('airline_id', $airline)->get();
    }

    public function store()
    {
        $this->validate();
        Flight::updateOrCreate(['id' => $this->flight_id], [
            'airline_id' => $this->airline_id,
            'flight_no' => $this->flight_no,
            'registration' => $this->registration,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'scheduled_time_arrival' => $this->scheduled_time_arrival,
            'scheduled_time_departure' => $this->scheduled_time_departure,
            'flight_type' => $this->flight_type,
        ]);

        $this->reset(['airline_id', 'flight_no', 'registration', 'origin', 'destination', 'scheduled_time_arrival', 'scheduled_time_departure', 'flight_type']);
        $this->dispatchBrowserEvent('closeModal');
        session()->flash('message', $this->flight_id ? 'Flight Updated Successfully.' : 'Flight Created Successfully.');
    }

    public function edit($id)
    {
        $flight = Flight::findOrFail($id);
        $this->flight_id = $id;
        $this->airline_id = $flight->airline_id;
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

    // Services Methods
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

    // Movement Methods
    public function saveMovements()
    {
        $validatedData = $this->validate(
            [
                'offblocks'  => 'nullable|date',
                'airborne'   => 'nullable|date',
                'touchdown'  => 'nullable|date',
                'onblocks'   => 'nullable|date',
                'passengers' => 'nullable|integer|min:0',
                'remarks'    => 'nullable|string',
                'flight_id'  => 'required|exists:flights,id',
            ]
        );
                
        if (!is_null($validatedData['offblocks']) || !is_null($validatedData['airborne']) || !is_null($validatedData['touchdown']) || !is_null($validatedData['onblocks'])) {
            
            Movement::create($validatedData);

            $flights = Flight::where('id', $validatedData['flight_id'])->first();
            $emailAddress = Route::with('emails')->where('airline_id', $flights->airline_id)->first()->emails->pluck('email')->toArray();
            $emailData = [
                'offblocks'  => $validatedData['offblocks'],
                'airborne'   => $validatedData['airborne'],
                'touchdown'  => $validatedData['touchdown'],
                'onblocks'   => $validatedData['onblocks'],
                'passengers' => $validatedData['passengers'],
                'remarks'    => $validatedData['remarks'],
                'flight_id'  => $validatedData['flight_id'],
                'flight_no'  => $flights->flight_no,
                'scheduled_time_departure' => $flights->scheduled_time_departure,
                'registration' => str_replace('-', '', $flights->registration),
                'destination' => $flights->destination,
                'flight_type' => $flights->flight_type,
                'recipients'  => $emailAddress
            ];

            Mail::send('mails.mvt', $emailData, function($message) use($emailData) {
                $message->subject('MVT '. $emailData['flight_no']);
                foreach ($emailData['recipients'] as $recipient) {
                    $message->bcc($recipient);
                }
            });
        }

        $this->dispatchBrowserEvent('closeModal');
        session()->flash('message', 'Movement created successfully.');
        $this->reset(['touchdown','onblocks','offblocks','airborne','passengers','remarks','flight_id']);
    }
}