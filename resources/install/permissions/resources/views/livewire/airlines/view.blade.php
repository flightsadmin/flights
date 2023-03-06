@section('title', __('Airlines'))
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div style="display: flex; justify-content: space-between; align-items: center;">
						<div class="float-left">
							<h4>Airlines </h4>
						</div>
						@if (session()->has('message'))
						<div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
						@endif
						<div>
							<input wire:model.debounce.500ms="keyWord" type="text" class="form-control form-control-sm" name="search" id="search" placeholder="Search Registration">
						</div>
						<div class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#dataModal">
							<i class="bi bi-plus-lg"></i>  Add Registration
						</div>
					</div>
				</div>
				<div class="card-body">
					@include('livewire.airlines.modals')
				<div class="table-responsive">
					<table class="table table-bordered table-sm">
						<thead class="thead">
							<tr> 
								<td>#</td> 
								<th>Airline</th>
								<th>IATA Code</th>
								<th>Base</th>
								<td>ACTIONS</td>
							</tr>
						</thead>
						<tbody>
							@forelse($airlines as $row)
							<tr>
								<td>{{ $loop->iteration }}</td> 
								<td>{{ $row->name }}</td>
								<td>{{ $row->iata_code }}</td>
								<td>{{ $row->base }}</td>
								<td width="90">
									<button class="btn btn-sm btn-danger" onclick="confirm('Confirm Delete Registration id {{$row->id}}? \nDeleted Registration cannot be recovered!')||event.stopImmediatePropagation()" wire:click="destroy({{$row->id}})"><i class="bi bi-trash3"></i> </button>  
								</td>
							</tr>
							@empty
							<tr>
								<td class="text-center" colspan="100%">No data Found </td>
							</tr>
							@endforelse
						</tbody>
					</table>						
					<div class="float-end">{{ $airlines->links() }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>