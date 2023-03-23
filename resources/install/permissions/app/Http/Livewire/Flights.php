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
    public $touchdown, $onblocks, $offblocks, $airborne, $passengers, $remarks, $linked_flight_id, $linked, $delaycodes = [], $delaydurations = [], $delaydescriptions = [];

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
                        ->paginate();
        return view('livewire.flights.view', [
            'airlines' => Airline::all(),
            'flights' => $flights,
            'selectedFlight' => $this->flight_id ? Flight::findOrFail($this->flight_id) : null,
        ]);
    }

    public function mount()
    {
        $this->selectedDate = Carbon::now()->format('Y-m-d');
        $this->linked = Flight::whereBetween('scheduled_time_departure', [Carbon::now(), Carbon::now()->copy()->addDay()])->get();
    }
    
    public function cancel()
    {
        $this->resetErrorBag();
        $this->reset(['flightFields', 'ServiceTypes', 'serviceList', 'flight_id']);
    }

    public function updatedairlineId($airline)
    {
        $this->registrations = Registration::where('airline_id', $airline)->get();
        $this->linked = Flight::whereBetween('scheduled_time_departure', [Carbon::now(), Carbon::now()->copy()->addDay()])
                        ->where('airline_id', $airline)
                        ->where('flight_type', 'departure')->get();
    }

    public function store()
    {
        $this->validate();
        $flight = Flight::updateOrCreate(['id' => $this->flight_id], [
            'airline_id' => $this->airline_id,
            'flight_no' => $this->flight_no,
            'registration' => $this->registration,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'scheduled_time_arrival' => $this->scheduled_time_arrival,
            'scheduled_time_departure' => $this->scheduled_time_departure,
            'flight_type' => $this->flight_type,
            'linked_flight_id' => $this->linked_flight_id
        ]);

        if ($this->linked_flight_id) {
            $arrFlight = Flight::findOrFail($this->linked_flight_id);
            $depFlight = Flight::findOrFail($flight->id);
            
            if ($arrFlight) {
                $flight->linkedFlight()->associate($arrFlight)->save();

                $arrFlight->linkedFlight()->associate($depFlight)->save();
            }
        }
        
        $this->reset(['linked_flight_id', 'airline_id', 'flight_no', 'registration', 'origin', 'destination', 'scheduled_time_arrival', 'scheduled_time_departure', 'flight_type']);
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

    public function saveMovements()
    {
        $validatedData = $this->validate(
            [
                'offblocks'             => 'nullable|date',
                'airborne'              => 'nullable|date',
                'touchdown'             => 'nullable|date',
                'onblocks'              => 'nullable|date',
                'passengers'            => 'nullable|integer|min:0',
                'remarks'               => 'nullable|string',
                'delaycodes.*'          => 'nullable|string',
                'delaydurations.*'      => 'nullable|string|min:4|max:4',
                'delaydescriptions.*'   => 'nullable|string',
                'flight_id'             => 'required|exists:flights,id',
            ]
        );
            // Pad the delay codes and durations arrays with null values if necessary
            $splitCodes         = array_pad($validatedData['delaycodes'] ?? [], 4, null);
            $splitDurations     = array_pad($validatedData['delaydurations'] ?? [], 4, null);
            $splitDescriptions  = array_pad($validatedData['delaydescriptions'] ?? [], 4, null);

        if (!is_null($validatedData['offblocks']) || !is_null($validatedData['airborne']) || !is_null($validatedData['touchdown']) || !is_null($validatedData['onblocks'])) {
            
            $mvt = Movement::create($validatedData);
            for ($i = 1; $i <= 4; $i++) {
                $mvt->{"delaycode$i"} = $splitCodes[$i - 1];
                $mvt->{"delayduration$i"} = !is_null($splitDurations[$i - 1]) ? date("H:i",strtotime($splitDurations[$i - 1])) : null;
                $mvt->{"delaydescription$i"} = $splitDescriptions[$i - 1];
            }
            $mvt->save();

            $flights = Flight::where('id', $validatedData['flight_id'])->first();
            $emailAddress = Route::with('emails')->where('airline_id', $flights->airline_id)->first()->emails->pluck('email')->toArray();
            $emailData = [
                'offblocks'                 => $validatedData['offblocks'],
                'airborne'                  => $validatedData['airborne'],
                'touchdown'                 => $validatedData['touchdown'],
                'onblocks'                  => $validatedData['onblocks'],
                'passengers'                => $validatedData['passengers'],
                'remarks'                   => $validatedData['remarks'],
                'flight_id'                 => $validatedData['flight_id'],
                'flight_no'                 => $flights->flight_no,
                'scheduled_time_arrival'    => $flights->scheduled_time_arrival,
                'scheduled_time_departure'  => $flights->scheduled_time_departure,
                'registration'              => str_replace('-', '', $flights->registration),
                'origin'                    => $flights->origin,
                'destination'               => $flights->destination,
                'flight_type'               => $flights->flight_type,
                'recipients'                => $emailAddress
            ];

            // Mail::send('mails.mvt', $emailData, function($message) use($emailData) {
            //     $message->subject('MVT '. $emailData['flight_no']);
            //     foreach ($emailData['recipients'] as $recipient) {
            //         $message->bcc($recipient);
            //     }
            // });
        }

        $this->dispatchBrowserEvent('closeModal');
        session()->flash('message', 'Movement created successfully.');
        $this->reset(['touchdown','onblocks','offblocks','airborne','passengers','remarks','flight_id', 'delaycodes', 'delaydurations', 'delaydescriptions']);
    }
}