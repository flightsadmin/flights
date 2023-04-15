<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Dompdf\Dompdf;
use App\Models\Route;
use App\Models\Flight;
use App\Models\Address;
use App\Models\Airline;
use App\Models\Service;
use Livewire\Component;
use App\Models\Movement;
use App\Models\FlightDelay;
use App\Models\ServiceList;
use App\Models\Registration;
use Livewire\WithPagination;
use App\Models\AirlineDelayCode;
use Illuminate\Support\Facades\Mail;

class Flights extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $airline_id, $flight_no, $registration, $origin, $destination, $scheduled_time_arrival, $scheduled_time_departure, $flight_type, $keyWord, $flight_id, $selectedDate, $mvt;
    public $ServiceTypes = [], $flightFields = [],$registrations = [], $delayCodes = [], $History, $outputdelay, $outputedelay, $outputdescription, $touchdown, $onblocks, $offblocks, $airborne, $passengers, $remarks;

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
            'airlines'          => Airline::orderBy('name', 'asc')->get(),
            'serviceList'       => ServiceList::orderBy('service', 'asc')->get(),
            'flights'           => $flights,
            'flightMvt'         => $this->flight_id ? $selectedFlight->movement()->latest()->first() : null,
            'selectedFlight'    => $selectedFlight,
            'airlineDelays'     => $this->flight_id ? AirlineDelayCode::where('airline_id', $selectedFlight->airline_id)->get() : null,

        ]);
    }

    public function mount()
    {
        $this->selectedDate = Carbon::now('Asia/Qatar')->format('Y-m-d');
    }
    
    public function emptyFields()
    {
        return [$this->resetErrorBag(), $this->reset([
            //Service Fields
            'flightFields', 'ServiceTypes', 'flight_id',
            //Flight Fields
            'airline_id', 'flight_no', 'registration', 'origin',
            'destination', 'scheduled_time_arrival', 'scheduled_time_departure', 'flight_type',
            //MVT Fields
            'touchdown', 'onblocks', 'offblocks', 'airborne', 'passengers', 'remarks', 'delayCodes'
            ])];
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
        $this->passengers   = $flight->passengers ?? null;
        $this->touchdown    = $flight->touchdown ?? null;
        $this->offblocks    = $flight->offblocks ?? null;
        $this->onblocks     = $flight->onblocks ?? null;
        $this->airborne     = $flight->airborne ?? null;
        $this->remarks      = $flight->remarks ?? null;
        
        $allDelays = $delays->pluck('duration', 'code')->toArray();
        $formattedDesc  = $delays->pluck('description')->toArray();
        
        $formattedDelay = array_chunk($allDelays, 2, true)[0] ?? [];
        $formattedeDelay = array_chunk($allDelays, 2, true)[1] ?? [];

        $this->outputdelay =  str_replace(':','', implode('/', array_merge(array_keys($formattedDelay), array_values($formattedDelay))));
        $this->outputedelay =  str_replace(':','', implode('/', array_merge(array_keys($formattedeDelay), array_values($formattedeDelay))));
        $this->outputdescription =  strtoupper(implode("\nSI ", array_values($formattedDesc)));

        foreach ($delays as $index => $delay) {
            $this->delayCodes[$index]['code'] = $delay->code;
            $this->delayCodes[$index]['duration'] = $delay->duration ?? '';
            $this->delayCodes[$index]['description'] = $delay->description ?? '';
        }
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
        $this->reset(['flightFields', 'ServiceTypes']);
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
        FlightDelay::where([['flight_id', $this->flight_id], ['code', $this->delayCodes[$index]['code']]])->delete();
        unset($this->delayCodes[$index]);
        $this->delayCodes = array_values($this->delayCodes);
        $this->viewFlight($this->flight_id);
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
                    'description'   => strtoupper($delay['description']),
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
        $address = Route::with('emails')->where('airline_id', $this->mvt->flight->airline_id)
                                        ->where('origin', $this->mvt->flight->origin)->first();
        $defaultAddress = ['george@flightadmin.info', 'flightsapps@gmail.com'];
        
        $emailData = [
            'mvt'               => $this->mvt,
            'flt'               => $this->mvt->flight,
            'flightTime'        => $address ? $address->flight_time : "00:45:00",
            'recipients'        => $address ? array_merge($address->emails->pluck('email')->toArray(), $defaultAddress) : $defaultAddress,
            'outputdelay'       => $this->outputdelay,
            'outputedelay'      => $this->outputedelay,
            'outputdescription' => $this->outputdescription,
        ];
        Mail::send('mails.mvt', $emailData, function($message) use($emailData) {
            $message->subject('MVT '. $emailData['flt']['flight_no']);
            $message->to(array_unique($emailData['recipients']));
        });
        $this->dispatchBrowserEvent('closeModal');
        session()->flash('message', 'Movement Sent successfully.');
        $this->emptyFields();
    }

    public function generatePDF()
    {
        $selectedFlight =  Flight::findOrFail($this->flight_id);

        $pdf = new Dompdf();
        $pdf->loadHtml(view('livewire.services.download', compact('selectedFlight'))->render());
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        $pdfData = $pdf->output();

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="services.pdf"',
        ];

        return response()->streamDownload(function() use ($pdfData) {
            echo $pdfData;
        }, 'services.pdf', $headers);
    }
}
