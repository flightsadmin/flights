<!-- Create / Edit Flight Modal -->
<div wire:ignore.self class="modal fade" id="dataModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg" role="document">
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
                        <label for="airline_id" class="form-label">Airline</label>
                        <select wire:model="airline_id" class="form-select  form-select-sm" id="airline_id">
                            <option value="">Choose an option...</option>
                            @foreach($airlines as $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach()
                        </select>
                        @error('airline_id') <span class="text-danger small">{{ $message }}</span> @enderror
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

<!-- Create / Edit Movements Modal -->
<div wire:ignore.self class="modal fade" id="mvtModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="dataModalLabel"> Send Movements Message </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <button class="position-absolute top-0 start-50 translate-middle btn btn-lg btn-success p-2" wire:loading wire:target="saveMovements">
                    Sending Movement ...
                </button>
                @if ($flight_id)
                    <div class="border px-3 mb-2">
                        <p>MVT</p>
                        <p>{{ $selectedFlight->flight_no }}/{{ date("d", strtotime($selectedFlight->scheduled_time_departure)) }}.{{ $selectedFlight->registration }}.{{ $selectedFlight->destination }}</p>                   
                        @if ($selectedFlight->flight_type == 'arrival')
                        <p>AA{{ date("Hi", strtotime($touchdown)) }}/{{ date("Hi", strtotime($onblocks)) }}</p>
                        @else
                        <p>AD{{ date("Hi", strtotime($offblocks)) }}/{{ date("Hi", strtotime($airborne)) }}</p>
                        <p>PX{{ $passengers }}</p>
                        <p>SI {{ strtoupper($remarks) }}</p>
                        @endif
                    </div>

                    <form>
                        <div class="row">
                            <input type="hidden" wire:model="flight_id">
                            @if ($selectedFlight->flight_type == 'arrival')
                            <div class="form-group col-md-4">
                                <label for="touchdown">Touchdown</label>
                                <input type="datetime-local" class="form-control" id="touchdown" wire:model="touchdown">
                                @error('touchdown') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="onblocks">On Blocks</label>
                                <input type="datetime-local" class="form-control" id="onblocks" wire:model="onblocks">
                                @error('onblocks') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            @else
                            <div class="form-group col-md-4">
                                <label for="offblocks">Off Blocks</label>
                                <input type="datetime-local" class="form-control" id="offblocks" wire:model="offblocks">
                                @error('offblocks') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="airborne">Airborne</label>
                                <input type="datetime-local" class="form-control" id="airborne" wire:model="airborne">
                                @error('airborne') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="passengers">Passengers</label>
                                <input type="number" class="form-control" id="passengers" wire:model="passengers">
                                @error('passengers') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            @endif
                        </div>

                        <div class="form-group col-md-12">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control" id="remarks" wire:model.lazy="remarks"></textarea>
                            @error('remarks') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </form>

                @else
                    <p>No Flight Selected</p>
                @endif
            </div>
            <div class="modal-footer p-0 mx-3 d-flex justify-content-between">
                <button data-bs-dismiss="modal" type="button" class="btn btn-sm btn-secondary bi bi-backspace-fill"> Close</button>
                <button wire:loading.attr="disabled" wire:click.prevent="saveMovements" type="button" class="btn btn-sm btn-primary bi bi-clock-history"> Send Movement</button>
            </div>
            <div class="card-body border">
                @if ($flight_id)
                    <i class="text-warning">History</i>
                    <table class="table table-sm table-bordered">
                        <tbody>
                            @forelse($selectedFlight->movement as $movement)
                                <tr>
                                    <td>
                                        <i class="bi bi-clock-history text-success"></i> Sent: {{ date("d-M-Y H:i:s", strtotime($movement->created_at)) }}
                                    </td>
                                    <td>
                                       <p>MVT</p>
                                       <p>{{ $selectedFlight->flight_no }}/{{ date("d", strtotime($selectedFlight->scheduled_time_departure)) }}.{{ $selectedFlight->registration }}.{{ $selectedFlight->destination }}</p>
                                        @if ($selectedFlight->flight_type == 'arrival')
                                        <p>AA{{ date("Hi", strtotime($movement->touchdown)) }}/{{ date("Hi", strtotime($movement->onblocks)) }}</p>
                                        @else
                                        <p>AD{{ date("Hi", strtotime($movement->offblocks)) }}/{{ date("Hi", strtotime($movement->airborne)) }}</p>
                                        <p>PX{{ $movement->passengers }}</p>
                                        <p>SI {{ strtoupper($movement->remarks) }}</p>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <P>No Movement message sent for this flight Yet</P>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <p>No Flights selected.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Success Message Toast  -->
<div  id="statusToast" class="toast position-fixed top-0 end-0 p-3 text-bg-success" style="margin-top:5px; margin-bottom:0px;" role="alert" aria-live="assertive" aria-atomic="true">
  <div class="toast-header text-bg-success">
    <i class="me-2 bi bi-send-fill"></i>
    <strong class="me-auto text-black">Success</strong>
    <small class="text-white">{{ now() }}</small>
    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
  <div class="toast-body text-black text-center">
    {{ session('message') }}
  </div>
</div>