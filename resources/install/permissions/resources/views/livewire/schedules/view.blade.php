@section('title', __('Flight'))
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h4>Flight Schedules </h4>
                        </div>
                        @if (session()->has('message'))
                        <div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
                        @endif
                        <div class="d-flex gap-1">
                            <form wire:submit.prevent="import" enctype="multipart/form-data">
                                <div class="d-flex gap-1">
                                    <input type="file" class="form-control form-control-sm mr-2" id="file" wire:model="file">
                                    @error('file') <span class="text-danger">{{ $message }}</span> @enderror
                                    <button type="submit" class="btn btn-success btn-sm bi bi-cloud-upload-fill"></button>
                                </div>
                            </form>
                            <button wire:click="downloadSample" class="btn btn-warning btn-sm bi bi-cloud-download-fill"> Download Sample</button>
                        </div>
                        <div class="row float-end">
                            <div class="col-md">
                                <label for="start_date">Start Date:</label>
                                <input type="date" wire:model="startDate" id="start_date" class="form-control form-control-sm">
                            </div>
                            <div class="col-md">
                                <label for="end_date">End Date:</label>
                                <input type="date" wire:model="endDate" id="end_date" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th width="120">Flight Number</th>
                                    <th width="220">Timings (Arrival & Departure)</th>
                                    <th width="120">Origin</th>
                                    <th width="120">Destination</th>
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
                                        <input type="text" required size="8" wire:model="flightFields.{{ $flightNumber }}.flight_no">
                                    </td>
                                    <td>
                                        <input type="time" required wire:model="flightFields.{{ $flightNumber }}.arrival">
                                        <input type="time" required wire:model="flightFields.{{ $flightNumber }}.departure">
                                    </td>
                                    <td>
                                        <input type="text" required size="8" wire:model="flightFields.{{ $flightNumber }}.origin">
                                    </td>
                                    <td>
                                        <input type="text" required size="8" wire:model="flightFields.{{ $flightNumber }}.destination">
                                    </td>
                                    @foreach ($days as $day)
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" wire:model="selectedDays" value="{{ $flightNumber }}-{{ $day }}" class="form-check-input">
                                        </div>
                                    </td>
                                    @endforeach
                                    <td>
                                        <a href="#" wire:click="removeFlights({{$index}})" class="text-danger bi bi-trash3"></a>
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