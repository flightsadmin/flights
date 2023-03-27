<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h4>Flight Schedules </h4>
                        </div>
                        <div class="d-flex gap-4">
                            <form wire:submit.prevent="import" enctype="multipart/form-data">
                                <div class="d-flex gap-4">
                                    <input type="file" class="form-control form-control-sm mr-2" id="file" wire:model="file">
                                    @error('file') <span class="text-danger small">{{ $message }}</span> @enderror
                                    <button type="submit" class="btn btn-success btn-sm bi bi-cloud-upload-fill"></button>
                                </div>
                            </form>
                            <button wire:click="downloadSample" class="btn btn-warning btn-sm bi bi-cloud-download-fill"> Download Sample</button>
                        </div>
                        <div class="row float-end">
                            <div class="col-md">
                                <label for="start_date">Start Date:</label>
                                <input type="date" wire:model="startDate" id="start_date" min="{{ date('Y-m-d', strtotime('-1 days')) }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-md">
                                <label for="end_date">End Date:</label>
                                <input type="date" wire:model="endDate" id="end_date" min="{{ date('Y-m-d', strtotime('+2 days')) }}" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th width="120">Airline</th>
                                    <th width="120">Flight Number</th>
                                    <th width="220">Timings (Arrival & Departure)</th>
                                    <th width="120">Origin</th>
                                    <th width="120">Destination</th>
                                    <th width="120">Type</th>
                                    @foreach ($days as $day)
                                    <th>{{ $day }}</th>
                                    @endforeach
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($flightNumbers as $index => $flightNumber)
                                <tr>
                                    <td>
                                        <select wire:model="flightFields.{{ $flightNumber }}.airline_id" class="form-select  form-select-sm">
                                            <option value="">--Select Airline--</option>
                                            @foreach($airlines as $value)
                                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                                            @endforeach()
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" size="8" wire:model="flightFields.{{ $flightNumber }}.flight_no">
                                    </td>
                                    <td>
                                        <input type="time" wire:model="flightFields.{{ $flightNumber }}.arrival">
                                        <input type="time" wire:model="flightFields.{{ $flightNumber }}.departure">
                                    </td>
                                    <td>
                                        <input type="text" size="8" wire:model="flightFields.{{ $flightNumber }}.origin">
                                    </td>
                                    <td>
                                        <input type="text" size="8" wire:model="flightFields.{{ $flightNumber }}.destination">
                                    </td>
                                    <td>
                                        <select wire:model="flightFields.{{ $flightNumber }}.flight_type" class="form-select  form-select-sm">
                                            <option value="">--Select Type--</option>
                                            <option value="arrival">Arrival</option>
                                            <option value="departure">Departure</option>                                            
                                        </select>
                                    </td>
                                    @foreach ($days as $day)
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" wire:model="selectedDays" value="{{ $flightNumber }}-{{ $day }}" class="form-check-input">
                                        </div>
                                    </td>
                                    @endforeach
                                    <td>
                                        <a href="" wire:click.prevent="removeFlights({{$index}})" class="text-danger bi bi-trash3"></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button wire:click.prevent="addFlights" class="btn btn-sm btn-secondary">+ Add a Schedule</button>
                    <button wire:click="createFlights" class="btn btn-sm btn-primary float-end">Create Flights</button>
                </div>
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