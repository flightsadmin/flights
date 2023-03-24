<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\Delay;
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
    public $ServiceTypes = [], $flightFields = [], $mvt, $serviceList = ["Pax Bus", "Crew Bus", "Pushback", "Cleaning", "Lavatory Service", "Passenger Steps"];
    public $touchdown, $showHistory = false, $onblocks, $offblocks, $airborne, $passengers, $remarks, $linked_flight_id, $linked, $delaycodes = [], $delaydurations = [], $delaydescriptions = [];

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
            'delays' => $this->flight_id ? Delay::where('airline_id', $selectedFlight->airline_id)->get() : null,

        ]);
    }

    public function mount()
    {
        $this->selectedDate = Carbon::now()->format('Y-m-d');
        $this->linked = Flight::whereDate('scheduled_time_departure',  $this->selectedDate)->get();
    }
    
    public function emptyFields()
    {
        return [$this->resetErrorBag(), $this->reset([
            //Service Fields
            'flightFields', 'ServiceTypes', 'serviceList', 'flight_id',
            //Flight Fields
            'linked_flight_id', 'airline_id', 'flight_no', 'registration', 'origin', 
            'destination', 'scheduled_time_arrival', 'scheduled_time_departure', 'flight_type',
            //MVT Fields
            'touchdown','onblocks','offblocks','airborne','passengers','remarks','flight_id', 'delaycodes', 'delaydurations', 'delaydescriptions'
            ])];
    }

    public function History()
    {
        $this->showHistory = !$this->showHistory;
    }

    public function updatedairlineId($airline)
    {
        $this->registrations = Registration::where('airline_id', $airline)->get();
        $this->linked = Flight::whereDate('scheduled_time_departure', $this->selectedDate)
                        ->where('airline_id', $airline)
                        ->where('flight_type', 'departure')->get();
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
            'flight_type'               => 'required|in:arrival,departure',
            'linked_flight_id'          => 'nullable|int'
        ]);
        $flight = Flight::updateOrCreate(['id' => $this->flight_id], $validatedData);

        if ($this->linked_flight_id) {
            $arrFlight = Flight::findOrFail($this->linked_flight_id);
            $depFlight = Flight::findOrFail($flight->id);
            
            if ($arrFlight) {
                $flight->linkedFlight()->associate($arrFlight)->save();

                $arrFlight->linkedFlight()->associate($depFlight)->save();
            }
        }
        
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
        $this->linked_flight_id = $flight->linked_flight_id;
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

        // $this->dispatchBrowserEvent('closeModal');
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
        $this->delaycodes[] = '';
        $this->delaydurations[] = null;
        $this->delaydescriptions[] = '';
    }

    public function removeDelay($index)
    {
        unset($this->delaycodes[$index]);
        $this->delaycodes = array_values($this->delaycodes);
        unset($this->delaydurations[$index]);
        $this->delaydurations = array_values($this->delaydurations);
        unset($this->delaydescriptions[$index]);
        $this->delaydescriptions = array_values($this->delaydescriptions);
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
            // Pad the delay codes and durations arrays with null values if necessary
            $splitCodes         = array_pad($this->delaycodes ?? [], 4, null);
            $splitDurations     = array_pad($this->delaydurations ?? [], 4, null);
            $splitDescriptions  = array_pad($this->delaydescriptions ?? [], 4, null);

        if (!is_null($validatedData['offblocks']) || !is_null($validatedData['airborne']) || !is_null($validatedData['touchdown']) || !is_null($validatedData['onblocks'])) {
            
            $movement = Movement::updateOrCreate($validatedData);
            
            for ($i = 1; $i <= 4; $i++) {
                $movement->{"delaycode$i"} = $splitCodes[$i - 1];
                $movement->{"delayduration$i"} = !is_null($splitDurations[$i - 1]) ? date("H:i",strtotime(str_pad(trim($splitDurations[$i - 1]), 4, '0', STR_PAD_LEFT))) : null;
                $movement->{"delaydescription$i"} = !is_null($splitDescriptions[$i - 1]) ? $splitDescriptions[$i - 1] : null;
            }
            $movement->save();
            // Arrange delay codes
            if ($movement) {
                $delayCodes = [];

                for ($i = 1; $i <= 4; $i++) {
                    $code = $movement->{"delaycode{$i}"};
                    $description = $movement->{"delaydescription{$i}"};
                    $duration = $movement->{"delayduration{$i}"};

                    if ($code && $duration) {
                        $delayCodes["dl{$i}"] = $code;
                        $delayCodes["duration{$i}"] = str_replace(':','',$duration);
                    } else {
                        $delayCodes["dl{$i}"] = "";
                        $delayCodes["duration{$i}"] = "";
                    }
                }

                $mergedDelayCodes = implode('/', [
                $delayCodes['dl1'],
                $delayCodes['dl2'],
                $delayCodes['dl3'],
                $delayCodes['dl4'],
                $delayCodes['duration1'],
                $delayCodes['duration2'],
                $delayCodes['duration3'],
                $delayCodes['duration4']
            ]);
            $this->mvt = $movement;
            $mergedDelayCodes = rtrim(preg_replace('/\/+/', '/', $mergedDelayCodes),'/');
            $this->delayCodes = $mergedDelayCodes;
            }
        }
        session()->flash('message', 'Movement created successfully.');
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
            'delays'        => $this->delayCodes,
        ];
        Mail::send('mails.mvt', $emailData, function($message) use($emailData) {
            $message->subject('MVT '. $emailData['flt']['flight_no']);
            foreach ($emailData['recipients'] as $recipient) {
                $message->bcc($recipient);
            }
        });
        $this->dispatchBrowserEvent('closeModal');
        $this->emptyFields();
    }
}