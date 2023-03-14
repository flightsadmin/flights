@section('title', __('Services'))
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h4>Flight Services </h4>
                        </div>
                        @if (session()->has('message'))
                        <div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
                        @endif
                        <div class="d-flex gap-1">
                            
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th width="200">Service</th>
                                    <th width="130">Start</th>
                                    <th width="130">Finish</th>
                                    <th></th>
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
                                        <input type="datetime-local" required wire:model="flightFields.{{ $actualService }}.start">
                                    </td>
                                    <td>
                                        <input type="datetime-local" required wire:model="flightFields.{{ $actualService }}.finish">
                                    </td>
                                    <td>
                                        <a href="#" wire:click="removeService({{$index}})" class="text-danger bi bi-trash3"></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button wire:click.prevent="addService" class="btn btn-sm btn-secondary">+ Add a Service</button>
                    <button wire:click.prevent="createServices" class="btn btn-sm btn-primary float-end">Create Service</button>
                </div>
            </div>
        </div>
    </div>
</div>