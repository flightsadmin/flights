<?php

namespace App\Http\Livewire;

use App\Models\Airline;
use Livewire\Component;
use App\Models\Registration;
use Livewire\WithPagination;

class Registrations extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $registration, $aircraft_type, $airline_id, $registration_id, $keyWord;

    protected $rules = [
        'registration'  => 'required',
        'aircraft_type' => 'required',
        'airline_id'    => 'required',
    ];

    public function cancel()
    {
        $this->reset();
    }

    public function store()
    {
        $this->validate();
        Registration::updateOrCreate(['id' => $this->registration_id], [
            'registration' => $this->registration,
            'aircraft_type' => $this->aircraft_type,
            'airline_id' => $this->airline_id,
        ]);

        $this->reset();
        $this->dispatchBrowserEvent('closeModal');
        session()->flash('message', $this->registration_id ? 'Registration Updated Successfully.' : 'Registration Created Successfully.');
    }

    public function edit($id)
    {
        $record = Registration::findOrFail($id);
        $this->registration_id = $id;
        $this->registration = $record->registration;
        $this->aircraft_type = $record->aircraft_type;
        $this->airline_id = $record->airline_id;
    }

    public function destroy($id)
    {
        Registration::find($id)->delete();
        session()->flash('message', 'Registration Deleted Successfully.');
    }

    public function render()
    {
        $keyWord = '%'. $this->keyWord .'%';
        $airlines = Airline::all();
        $reglists = Registration::distinct('aircraft_type')->pluck('aircraft_type');
        $registrations = Registration::with('airline')
                        ->orWhere('registration', 'LIKE', $keyWord)
                        ->paginate(10);
        return view('livewire.registrations.view', [
            'registrations' => $registrations,
            'airlines' => $airlines,
            'reglists' => $reglists,
        ]);
    }
}