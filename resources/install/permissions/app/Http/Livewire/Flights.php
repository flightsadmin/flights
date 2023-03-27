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
use App\Models\Flightdelay;
use App\Models\Registration;
use Livewire\WithPagination;
use App\Models\AirlineDelayCode;
use Illuminate\Support\Facades\Mail;

class Flights extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $registrations = [], $airline_id, $flight_no, $registration, $origin, $destination, $scheduled_time_arrival, $scheduled_time_departure, $flight_type, $keyWord, $flight_id, $selectedDate;
    public $ServiceTypes = [], $flightFields = [], $mvt, $serviceList = ["Pax Bus", "Crew Bus", "Pushback", "Cleaning", "Lavatory Service", "Passenger Steps"];
    public $showHistory = false, $outputde, $outputdl, $touchdown, $onblocks, $offblocks, $airborne, $passengers, $remarks, $delayCodes = [];

    protected $listeners = ['refreshItems' => '$refresh'];

    public function render()
    {
        $keyWord = '%'. $this->keyWord .'%';
        $selectedFlight =  $this->flight_id ? Flight::findOrFail($this->flight_id) : null;
        $flights = Flight::with('service', 'movement')
                        ->whereDate('scheduled_time_departure', $this->selectedDate)
                        ->where('flight_no', 'LIKE', $keyWord)
                        ->orderBy('scheduled_time_departure', 'asc')
                        ->paginate();
        return view('livewire.flights.view', [
            'airlines'  => Airline::all(),
            'flights'   => $flights,
            'flightMvt' => $this->flight_id ? $selectedFlight->movement()->latest()->first() : null,
            'selectedFlight' => $selectedFlight,
            'delays' => $this->flight_id ? AirlineDelayCode::where('airline_id', $selectedFlight->airline_id)->get() : null,

        ]);
    }

    public function mount()
    {
        $this->selectedDate = Carbon::now()->format('Y-m-d');
    }
    
    public function emptyFields()
    {
        return [$this->resetErrorBag(), $this->reset([
            //Service Fields
            'flightFields', 'ServiceTypes', 'serviceList', 'flight_id',
            //Flight Fields
            'airline_id', 'flight_no', 'registration', 'origin', 
            'destination', 'scheduled_time_arrival', 'scheduled_time_departure', 'flight_type',
            //MVT Fields
            'touchdown','onblocks','offblocks','airborne','passengers','remarks','flight_id', 'delayCodes'
            ])];
    }

    public function History()
    {
        $this->showHistory = !$this->showHistory;
    }

    public function updatedairlineId($airline)
    {
        $this->registrations = Registration::where('airline_id', $airline)->get();
    }

    public function store()
    {
        $validatedData = $this->validate([
            'airline_id'                => 'required|int',
            'flight_no'                 => 'required|string',
            'registration'              => 'required|string',
            'origin'                    => 'required|string',
            'destination'               => 'required|string',
            'scheduled_time_arrival'    => 'required|date',
            'scheduled_time_departure'  => 'required|date',
            'flight_type'               => 'required|in:arrival,departure'
        ]);
        $flight = Flight::updateOrCreate(['id' => $this->flight_id], $validatedData);
        
        $this->emptyFields();
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

        $flight = Movement::where('flight_id', $id)->latest()->first();
        $delays = FlightDelay::where('flight_id', $id)->latest()->take(4)->get();
        $this->passengers = $flight->passengers ?? null;
        $this->touchdown = $flight->touchdown ?? null;
        $this->offblocks = $flight->offblocks ?? null;
        $this->onblocks = $flight->onblocks ?? null;
        $this->airborne = $flight->airborne ?? null;
        $this->remarks = $flight->remarks ?? null;
        
        $formattedDelay = $delays->pluck('duration', 'code')->toArray();
        $formattedDesc  = $delays->pluck('description')->toArray();

        $this->outputdl =  str_replace(':','', implode('/', array_merge(array_keys($formattedDelay), array_values($formattedDelay))));
        $this->outputde =  strtoupper(implode("\nSI ", array_values($formattedDesc)));

        foreach ($delays as $index => $delay) {
            $this->delayCodes[$index]['code'] = $delay->code;
            $this->delayCodes[$index]['duration'] = $delay->duration ?? '';
            $this->delayCodes[$index]['description'] = $delay->description ?? '';
        }
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
        session()->flash('message', 'Service Added Successfully.');
        $this->reset(['flightFields', 'ServiceTypes', 'serviceList']);
    }

    public function destroyService($flight)
    {
        Service::where([['flight_id', $this->flight_id], ['service_type', $flight]])->delete();
        session()->flash('message', 'Service Deleted Successfully.');
    }

    // Movement Methods
    public function addDelay()
    {
        $this->delayCodes[] = ['code' => '', 'duration' => '', 'description' => ''];
    }

    public function removeDelay($index)
    {
        
        unset($this->delayCodes[$index]);
        $this->delayCodes = array_values($this->delayCodes);
    }

    public function saveMovement()
    {
        $validatedData = $this->validate(
            [
                'offblocks'             => 'nullable|date',
                'airborne'              => 'nullable|date',
                'touchdown'             => 'nullable|date',
                'onblocks'              => 'nullable|date',
                'passengers'            => 'nullable|integer|min:0',
                'remarks'               => 'nullable|string',
                'flight_id'             => 'required|exists:flights,id',
            ]
        );

        if (!is_null($validatedData['offblocks']) || !is_null($validatedData['airborne']) || !is_null($validatedData['touchdown']) || !is_null($validatedData['onblocks'])) {
            
            $movement = Movement::updateOrCreate(['flight_id' => $validatedData['flight_id']], $validatedData);
            foreach ($this->delayCodes as $delay) {
                $movement->flight->delays()->updateOrCreate(['code' => $delay['code'], 'flight_id' => $validatedData['flight_id']],
                [
                    'code'          => $delay['code'],
                    'duration'      => date("H:i", strtotime(str_pad(trim($delay['duration']), 4, '0', STR_PAD_LEFT))),
                    'description'   => $delay['description'],
                    'flight_id'     => $validatedData['flight_id']
                ]);
            };
            $this->mvt = $movement;
        }
        $this->viewFlight($this->flight_id);
    }
    
    public function sendMovement()
    {
        $this->saveMovement();
        $address = Route::with('emails')->where('airline_id', $this->mvt->flight->airline_id)->first();
        $emailAddresses = $address->emails->pluck('email')->toArray();

        $emailData = [
            'mvt'           => $this->mvt,
            'flt'           => $this->mvt->flight,
            'flightTime'    => $address->flight_time,
            'recipients'    => $emailAddresses,
        ];
        Mail::send('mails.mvt', $emailData, function($message) use($emailData) {
            $message->subject('MVT '. $emailData['flt']['flight_no']);
            foreach ($emailData['recipients'] as $recipient) {
                $message->bcc($recipient);
            }
        });
        $this->dispatchBrowserEvent('closeModal');
        session()->flash('message', 'Movement Sent successfully.');
        $this->emptyFields();
    }
}