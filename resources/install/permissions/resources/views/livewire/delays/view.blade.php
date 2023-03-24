<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div style="display: flex; justify-content: space-between; align-items: center;">
						<div class="float-left">
							<h4>Delay Codes </h4>
						</div>
						<div>
							<select wire:model="keyWord" class="form-select  form-select-sm" id="airline_id">
                                <option value="">Filter By Airline...</option>
                                @foreach($airlines as $value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach()
                            </select>
						</div>
                        <div class="d-flex gap-4">
                            <form wire:submit.prevent="importDelays" enctype="multipart/form-data">
                                <div class="d-flex gap-4">
                                    <input type="file" class="form-control form-control-sm mr-2" id="file" wire:model="file">
                                    @error('file') <span class="text-danger small">{{ $message }}</span> @enderror
                                    <button type="submit" class="btn btn-success btn-sm bi bi-cloud-upload-fill"></button>
                                </div>
                            </form>
							<button wire:click="downloadDelays" class="btn btn-warning btn-sm bi bi-cloud-download-fill"> Download Sample</button>
                        </div>
						<div class="btn btn-sm btn-info bi bi-plus-lg" data-bs-toggle="modal" data-bs-target="#dataModal">
							 Add Delay Code
						</div>
					</div>
				</div>
				<div class="card-body">
					@include('livewire.delays.modals')
				<div class="table-responsive">
					<table class="table table-bordered table-sm">
						<thead class="thead">
							<tr> 
								<td>#</td> 
								<th>Numeric Code</th>
								<th>Alpha Code</th>
								<th>Description</th>
								<th>Accountable</th>
								<th>Airline</th>
								<td>ACTIONS</td>
							</tr>
						</thead>
						<tbody>
							@forelse($delays as $row)
							<tr>
								<td>{{ $loop->iteration }}</td> 
								<td>{{ $row->numeric_code }}</td>
								<td>{{ $row->alpha_numeric_code }}</td>
								<td>{{ $row->description }}</td>
								<td>{{ $row->accountable }}</td>
								<td>{{ $row->airline->name }}</td>
								<td width="90">
									<button class="btn btn-danger custom-btn-sm bi bi-trash3" onclick="confirm('Confirm Delete Airline id {{$row->id}}? \nDeleted Airline cannot be recovered!')||event.stopImmediatePropagation()" wire:click="destroy({{$row->id}})"> </button>  
								</td>
							</tr>
							@empty
							<tr>
								<td class="text-center" colspan="100%">No Delays Found </td>
							</tr>
							@endforelse
						</tbody>
					</table>
					<div class="float-end">{{ $delays->links() }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>