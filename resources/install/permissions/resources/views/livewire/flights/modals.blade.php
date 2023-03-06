<!-- Create / Edit Flight Modal -->
<div wire:ignore.self class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel">
                    {{ $flight_id ? 'Edit Flight' : 'Create New Flight' }}  
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="flight_no" class="form-label">Flight Number</label>
                        <input wire:model.lazy="flight_no" type="text" class="form-control form-control-sm" id="flight_no">
                        @error('flight_no') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="registration" class="form-label">Registration</label>
                        <select wire:model.lazy="registration" class="form-select  form-select-sm" id="registration">
                            <option value="">Choose an option...</option>
                            @foreach($registrations as $value)
                            <option value="{{ $value->registration }}">{{ $value->registration }} - {{ $value->aircraft_type }}</option>
                            @endforeach()
                        </select>
                        @error('registration') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="origin" class="form-label">Origin</label>
                        <input wire:model.lazy="origin" type="text" class="form-control form-control-sm" id="origin">
                        @error('origin') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="destination" class="form-label">Destination</label>
                        <input wire:model.lazy="destination" type="text" class="form-control form-control-sm" id="destination">
                        @error('destination') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="scheduled_time_arrival" class="form-label">Scheduled Time of Arrival</label>
                        <input wire:model.lazy="scheduled_time_arrival" type="datetime-local" class="form-control form-control-sm" id="scheduled_time_arrival">
                        @error('scheduled_time_arrival') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="scheduled_time_departure" class="form-label">Scheduled Time of Departure</label>
                        <input wire:model.lazy="scheduled_time_departure" type="datetime-local" class="form-control form-control-sm" id="scheduled_time_departure">
                        @error('scheduled_time_departure') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="flight_type" class="form-label">Flight Type</label>
                        <select wire:model.lazy="flight_type" class="form-select form-select-sm" id="flight_type">
                            <option value="">Select Flight Type</option>
                            <option value="arrival">Arrival</option>
                            <option value="departure">Departure</option>
                        </select>
                        @error('flight_type') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button wire:click.prevent="store" type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- View Flight Modal -->
<div wire:ignore.self class="modal fade" id="viewModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">View Flight</h5>
                <button wire:click.prevent="cancel()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                @if ($selectedFlightId)
                    <p>{{ $selectedFlight->flight_no }}</p>
                    <p>{{ $selectedFlight->registration }}</p>
                    <p>{{ $selectedFlight->scheduled_time_arrival }}</p>
                    <p>{{ $selectedFlight->scheduled_time_departure }}</p>
                    <p>{{ $selectedFlight->origin }}</p>
                    <p>{{ $selectedFlight->destination }}</p>
                @else
                    <p>No Flights selected.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" wire:click.prevent="cancel()" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>