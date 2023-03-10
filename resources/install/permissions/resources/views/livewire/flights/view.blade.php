@section('title', __('Flight'))
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div style="display: flex; justify-content: space-between; align-items: center;">
						<div class="float-left">
							<h4>Flights</h4>
						</div>
						@if (session()->has('message'))
						<div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
						@endif
						<div>
							<input wire:model.debounce.500ms="keyWord" type="text" class="form-control form-control-sm" name="search" id="search" placeholder="Search Flights">
						</div>
						<div>
							<input type="date" wire:model="selectedDate" id="datepicker" class="form-control form-control-sm">
						</div>
						<div class="d-flex gap-2">
							@role('super-admin|admin')
								<a href="{{ url('/schedules') }}" class="btn btn-sm btn-warning bi bi-newspaper"> Generate Schedule </a>
							@endrole
							<div class="btn btn-sm btn-info bi bi-plus-lg" data-bs-toggle="modal" data-bs-target="#dataModal">
								 Add Flight
							</div>
						</div>
					</div>
				</div>
				<div class="card-body">
					@include('livewire.flights.modals')
				<div class="table-responsive">
					<table class="table table-bordered table-sm">
						<thead class="thead">
							<tr> 
								<td>#</td> 
								<th>Flight No</th>
								<th>Registration</th>
								<th>STA</th>
								<th>STD</th>
								<th>Origin</th>
								<th>Destination</th>
								<td>ACTIONS</td>
							</tr>
						</thead>
						<tbody>
							@forelse($flights as $row)
							<tr>
								<td>{{ $loop->iteration }}</td> 
								<td>{{ $row->flight_no }}</td>
								<td>{{ $row->registration }}</td>
								<td>{{ $row->scheduled_time_arrival }}</td>
								<td>{{ $row->scheduled_time_departure }}</td>
								<td>{{ $row->origin }}</td>
								<td>{{ $row->destination }}</td>
								<td width="90">
									<div class="dropdown">
										<a class="btn btn-sm btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
											Actions
										</a>
										<ul class="dropdown-menu">
											<li><a data-bs-toggle="modal" data-bs-target="#viewModal" class="dropdown-item bi bi-eye-fill" wire:click="viewFlight({{ $row->id }})" > View </a></li>
											<li><a data-bs-toggle="modal" data-bs-target="#dataModal" class="dropdown-item bi bi-pencil-square" wire:click="edit({{$row->id}})"> Edit </a></li>
											<li><a class="dropdown-item bi bi-trash3" onclick="confirm('Confirm Delete Flight id {{$row->id}}? \nDeleted Flight cannot be recovered!')||event.stopImmediatePropagation()" wire:click="destroy({{$row->id}})"> Delete </a></li>  
										</ul>
									</div>								
								</td>
							</tr>
							@empty
							<tr>
								<td class="text-center" colspan="100%">No Flights Found </td>
							</tr>
							@endforelse
						</tbody>
					</table>						
					<div class="float-end">{{ $flights->links() }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>