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
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="airline_id" class="form-label">Airline</label>
                            <select wire:model="airline_id" class="form-select  form-select-sm" id="airline_id">
                                <option value="">Choose an option...</option>
                                @foreach($airlines as $value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach()
                            </select>
                            @error('airline_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="flight_no" class="form-label">Flight Number</label>
                            <input wire:model.lazy="flight_no" type="text" class="form-control form-control-sm" id="flight_no">
                            @error('flight_no') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="registration" class="form-label">Registration</label>
                            <select wire:model.lazy="registration" class="form-select  form-select-sm" id="registration">
                                <option value="">Choose an option...</option>
                                @foreach($registrations as $value)
                                <option value="{{ $value->registration }}">{{ $value->registration }}</option>
                                @endforeach()
                            </select>
                            @error('registration') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="flight_type" class="form-label">Flight Type</label>
                            <select wire:model.lazy="flight_type" class="form-select form-select-sm" id="flight_type">
                                <option value="">Select Flight Type</option>
                                <option value="arrival">Arrival</option>
                                <option value="departure">Departure</option>
                            </select>
                            @error('flight_type') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div> 
                        <div class="col-md-6 mb-3">
                            <label for="origin" class="form-label">Origin</label>
                            <input wire:model.lazy="origin" type="text" class="form-control form-control-sm" id="origin">
                            @error('origin') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="destination" class="form-label">Destination</label>
                            <input wire:model.lazy="destination" type="text" class="form-control form-control-sm" id="destination">
                            @error('destination') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="scheduled_time_arrival" class="form-label">Scheduled Time of Arrival</label>
                            <input wire:model.lazy="scheduled_time_arrival" type="datetime-local" class="form-control form-control-sm" id="scheduled_time_arrival">
                            @error('scheduled_time_arrival') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="scheduled_time_departure" class="form-label">Scheduled Time of Departure</label>
                            <input wire:model.lazy="scheduled_time_departure" type="datetime-local" class="form-control form-control-sm" id="scheduled_time_departure">
                            @error('scheduled_time_departure') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex align-items-center justify-content-between">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                <button wire:click.prevent="store" type="button" class="btn btn-sm btn-primary bi bi-check2-circle"> Save</button>
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
                <button type="button" class="btn-close" wire:click.prevent="emptyFields()" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                    <td class="d-flex align-items-center justify-content-between">
                                        <p>{{ $index + 1 }}. {{ $service->service_type }} ({{ $service->start }} - {{ $service->finish }})</p>
                                        <a href="" wire:click.prevent="destroyService('{{ $service->service_type }}')" class="text-danger bi bi-trash3 text-end px-2"></a>
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
                                        <a href="" wire:click.prevent="removeService({{$index}})" class="text-danger bi bi-trash3-fill"></a>
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
                <button type="button" wire:click.prevent="emptyFields()" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if ($flight_id)
                    <div class="border px-3 mb-2">
                        <i class="text-warning bi bi-clock-history"> Last Saved Movement</i>
                        <p>MVT</p>
                        {{ $selectedFlight->flight_no }}/{{ ($selectedFlight->flight_type == 'arrival') ? 
                            date("d", strtotime($selectedFlight->scheduled_time_arrival)) . "." . $selectedFlight->registration . "." . $selectedFlight->destination : 
                            date("d", strtotime($selectedFlight->scheduled_time_departure)) . "." . $selectedFlight->registration . "." . $selectedFlight->origin }}</p>
                        @if ($selectedFlight->flight_type == 'arrival')
                        <p>AA{{ date("Hi", strtotime($flightMvt->touchdown ?? null)) }}/{{ date("Hi", strtotime($flightMvt->onblocks  ?? null)) }}</p>
                        @else
                        <p>AD{{ date("Hi", strtotime($flightMvt->offblocks ?? null)) }}/{{ date("Hi", strtotime($flightMvt->airborne ?? null)) }}
                        EA{{ date("Hi", strtotime($flightMvt->airborne ?? null)+strtotime($flightMvt->airborne ?? null)) }} {{ $selectedFlight->destination }}</p>
                        @if (empty($outputdl))  @else {{ "DL" .$outputdl ?? null }} @endif
                        <p>PX{{ $flightMvt->passengers ?? null }}</p>
                        @if (empty($outputde))  @else {!! "SI ". nl2br(e($outputde)) ?? null !!} @endif
                        <p>@if (empty($flightMvt->remarks))  @else SI {{ strtoupper($flightMvt->remarks ?? null) }} @endif</p>
                        @endif                     
                    </div>

                    <form>
                        <div class="row">
                            <input type="hidden" wire:model="flight_id">
                            @if ($selectedFlight->flight_type == 'arrival')
                            <div class="form-group col-md-4">
                                <label for="touchdown">Touchdown</label>
                                <input type="datetime-local" class="form-control" id="touchdown" wire:model="touchdown">
                                @error('touchdown') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="onblocks">On Blocks</label>
                                <input type="datetime-local" class="form-control" id="onblocks" wire:model="onblocks">
                                @error('onblocks') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            @else
                            <div class="form-group col-md-4">
                                <label for="offblocks">Off Blocks</label>
                                <input type="datetime-local" class="form-control" id="offblocks" wire:model="offblocks">
                                @error('offblocks') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="airborne">Airborne</label>
                                <input type="datetime-local" class="form-control" id="airborne" wire:model="airborne">
                                @error('airborne') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="passengers">Passengers</label>
                                <input type="number" class="form-control" id="passengers" wire:model="passengers">
                                @error('passengers') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group col-md-12">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control" id="remarks" wire:model.lazy="remarks"></textarea>
                                @error('remarks') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            @endif
                        </div>

                        @if ($selectedFlight->flight_type == 'departure')
                            <div>
                                <div class="form-group col-md-12 my-3">
                                    <label>Delay Codes</label>
                                    @foreach ($delayCodes as $index => $delayCode)
                                        <div class="d-flex gap-1 mb-1">
                                            <select wire:model="delayCodes.{{ $index }}.code" class="form-select  form-select-sm">
                                                <option value="">Choose an option...</option>
                                                @foreach($delays as $value)
                                                    <option value="{{ $value->numeric_code }}"> {{ $value->alpha_numeric_code }} - {{ Str::limit($value->description, 50) }} </option>
                                                @endforeach
                                            </select>
                                            <input maxlength="5" class="form-control form-control-sm" type="text" wire:model="delayCodes.{{ $index }}.duration" placeholder="0000">
                                            <input class="form-control form-control-sm" type="text" wire:model="delayCodes.{{ $index }}.description" placeholder="Decription">
                                            <a href="" class="bi bi-trash3-fill text-danger text-center px-4" wire:click.prevent="removeDelay({{ $index }})"></a>
                                        </div>
                                        @error('delayCodes.'.$index.'.duration') <span class="text-danger small">{{ $message }}</span> @enderror
                                    @endforeach
                                    @if (count($delayCodes) < 4 )
                                        <button class="btn custom-btn-sm btn-secondary bi bi-plus-circle" type="button" wire:click.prevent="addDelay"> Add Delay</button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </form>
                @else
                    <p>No Flight Selected</p>
                @endif
            </div>
            <div wire:loading wire:target="sendMovement">
                <div class="custom-spin-overlay">
                    <div class="position-absolute top-50 start-50 translate-middle d-flex justify-content-center">
                        <div class="spinner-border" style="width: 6rem; height: 6rem;" role="status">
                            <span class="visually-hidden">Sending Movement ...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-0 mx-3 d-flex align-items-center justify-content-between">
                <button wire:click.prevent="emptyFields" data-bs-dismiss="modal" type="button" class="btn btn-sm btn-secondary bi bi-backspace-fill"> Close</button>
                <button wire:loading.attr="disabled" wire:click.prevent="sendMovement" type="button" class="btn btn-sm btn-success bi bi-envelope-check-fill"> Send Movement</button>
                <button wire:loading.attr="disabled" wire:click.prevent="saveMovement" type="button" class="btn btn-sm btn-primary bi bi-clock-history"> Save Movement</button>
            </div>
            <div class="card-body border">
                @if ($flight_id)
                    <i class="text-warning"> History <a wire:click="History" class="bi bi-eye h5 text-info"></a></i> 
                        @if($showHistory)
                        <div class="row">
                            @forelse($selectedFlight->movement as $movement)
                            <div class="col-md-4 border p-2">
                                <b><i class="bi bi-clock-history text-success"></i> <u>Sent: {{ date("d-M-Y H:i:s", strtotime($movement->created_at)) }}</u></b>
                                <p>MVT</p>
                                {{ $selectedFlight->flight_no }}/{{ ($selectedFlight->flight_type == 'arrival') ? 
                                    date("d", strtotime($selectedFlight->scheduled_time_arrival)) . "." . $selectedFlight->registration . "." . $selectedFlight->destination : 
                                    date("d", strtotime($selectedFlight->scheduled_time_departure)) . "." . $selectedFlight->registration . "." . $selectedFlight->origin }}</p>
                                    @if ($selectedFlight->flight_type == 'arrival')
                                    <p>AA{{ date("Hi", strtotime($movement->touchdown)) }}/{{ date("Hi", strtotime($movement->onblocks)) }}</p>
                                    @else
                                    <p>AD{{ date("Hi", strtotime($movement->offblocks)) }}/{{ date("Hi", strtotime($movement->airborne)) }}
                                        EA{{ date("Hi", strtotime($movement->airborne)+strtotime($movement->airborne)) }} {{ $selectedFlight->destination }}</p>
                                        @if (empty($outputdl))  @else {{ "DL" .$outputdl ?? null }} @endif
                                        <p>PX{{ $movement->passengers ?? null }}</p>
                                        @if (empty($outputde))  @else {!! "SI ". nl2br(e($outputde)) ?? null !!} @endif
                                        <p>@if (empty($flightMvt->remarks))  @else SI {{ strtoupper($movement->remarks ?? null) }} @endif</p>
                                    @endif
                                    </div>
                            @empty
                                <h5 class="text-center">No Movements Sent for this flight </h5>
                            @endforelse
                        </div>
                        @endif    
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