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
							<input wire:model.debounce.500ms="keyWord" type="text" class="form-control form-control-sm" name="search" id="search" placeholder="Search Flight">
						</div>
						<div class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#dataModal">
						<i class="bi bi-plus-lg"></i>  Add Flight
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
											<li><a data-bs-toggle="modal" data-bs-target="#viewModal" class="dropdown-item" wire:click="viewFlight({{ $row->id }})" ><i class="bi bi-eye-fill"></i> View </a></li>
											<li><a data-bs-toggle="modal" data-bs-target="#dataModal" class="dropdown-item" wire:click="edit({{$row->id}})"><i class="bi bi-pencil-square"></i> Edit </a></li>
											<li><a class="dropdown-item" onclick="confirm('Confirm Delete Flight id {{$row->id}}? \nDeleted Flight cannot be recovered!')||event.stopImmediatePropagation()" wire:click="destroy({{$row->id}})"><i class="bi bi-trash3"></i> Delete </a></li>  
										</ul>
									</div>								
								</td>
							</tr>
							@empty
							<tr>
								<td class="text-center" colspan="100%">No data Found </td>
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