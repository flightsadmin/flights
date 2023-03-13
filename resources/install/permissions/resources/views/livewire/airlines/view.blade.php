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
                        <div class="d-flex gap-1">
                            <form wire:submit.prevent="import" enctype="multipart/form-data">
                                <div class="d-flex gap-1">
                                    <input type="file" class="form-control form-control-sm mr-2" id="file" wire:model="file">
                                    @error('file') <span class="text-danger">{{ $message }}</span> @enderror
                                    <button type="submit" class="btn btn-success btn-sm bi bi-cloud-upload-fill"></button>
                                </div>
                            </form>
							<button wire:click="downloadAirlines" class="btn btn-warning btn-sm bi bi-cloud-download-fill"> Download Sample</button>
                        </div>
						<div class="btn btn-sm btn-info bi bi-plus-lg" data-bs-toggle="modal" data-bs-target="#dataModal">
							 Add Registration
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
									<button class="btn btn-sm btn-danger bi bi-trash3" onclick="confirm('Confirm Delete Registration id {{$row->id}}? \nDeleted Registration cannot be recovered!')||event.stopImmediatePropagation()" wire:click="destroy({{$row->id}})"> </button>  
								</td>
							</tr>
							@empty
							<tr>
								<td class="text-center" colspan="100%">No Airlines Found </td>
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