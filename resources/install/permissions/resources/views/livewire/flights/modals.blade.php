<!-- Create / Edit Flight Modal -->
<div wire:ignore.self class="modal fade" id="dataModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
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
                        <label for="selectedAirline" class="form-label">Airline</label>
                        <select wire:model="selectedAirline" class="form-select  form-select-sm" id="selectedAirline">
                            <option value="">Choose an option...</option>
                            @foreach($airlines as $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach()
                        </select>
                        @error('selectedAirline') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
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
                            <option value="{{ $value->registration }}">{{ $value->registration }}</option>
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


<!-- Create / Edit Sevices Modal -->
<div wire:ignore.self class="modal fade" id="viewModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="dataModalLabel"> Flight Services </h6>
                <button type="button" class="btn-close" wire:click="cancel()" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body border">
                    @if ($flight_id)
                        <h5>{{ $selectedFlight->flight_no }}  <span class="text-secondary bi bi-send-check h6"></span>  {{ $selectedFlight->registration }}</h5>
                        <b class="text-success bi bi-arrow-down-right-circle-fill"></b> {{ $selectedFlight->origin }} {{ $selectedFlight->scheduled_time_departure }}
                        <b class="text-warning bi bi-arrow-up-right-circle-fill"> </b>{{ $selectedFlight->destination }} {{ $selectedFlight->scheduled_time_arrival }}
                        <hr >
                        <b>Services</b>
                        <table class="table table-sm table-bordered">
                            <tbody>
                                @forelse ($selectedFlight->service as $index => $service)
                                <tr>
                                    <td>
                                        <p>{{ $index + 1 }}. {{ $service->service_type }} ({{ $service->start }} - {{ $service->finish }})</p>
                                    </td>
                                    <td class="d-flex gap-2">
                                        <a href="#" wire:click="destroyService('{{ $service->service_type }}')" class="text-danger bi bi-trash3 text-end"></a>
                                    </td>
                                </tr>
                                @empty
                                <P>No Services for this flight Yet</P>
                                @endforelse
                            </tbody>
                        </table>
                    @else
                        <p>No Flights selected.</p>
                    @endif
                </div>
                <div class="card-body border">
                    <b>Add Services</b>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Start</th>
                                    <th>Finish</th>
                                    <th width="30"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ServiceTypes as $index => $actualService)
                                <tr>
                                    <td>
                                        <select wire:model="flightFields.{{ $actualService }}.service_type" class="form-select  form-select-sm" id="registration">
                                            <option value="">Select a Service...</option>
                                            @foreach($serviceList as $value)
                                            <option value="{{ $value }}">{{ $value }}</option>
                                            @endforeach()
                                        </select>
                                    </td>
                                    <td>
                                        <input class="form-control  form-control-sm" type="datetime-local" required wire:model="flightFields.{{ $actualService }}.start">
                                    </td>
                                    <td>
                                        <input class="form-control  form-control-sm" type="datetime-local" required wire:model="flightFields.{{ $actualService }}.finish">
                                    </td>
                                    <td>
                                        <a href="#" wire:click="removeService({{$index}})" class="text-danger bi bi-trash3"></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button wire:click.prevent="addService" class="btn btn-sm btn-secondary bi bi-plus-lg"> Add a Service</button>
                        <button wire:click.prevent="createServices" class="btn btn-sm btn-primary float-end bi bi-check2-circle"> Create Service</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
