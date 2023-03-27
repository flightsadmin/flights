<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div style="display: flex; justify-content: space-between; align-items: center;">
						<div class="float-left">
							<h4>Airlines </h4>
						</div>
						<div>
							<input wire:model.debounce.500ms="keyWord" type="text" class="form-control form-control-sm" name="search" id="search" placeholder="Search Airline">
						</div>
                        <div class="d-flex gap-4">
                            <form wire:submit.prevent="import" enctype="multipart/form-data">
                                <div class="d-flex gap-4">
                                    <input type="file" class="form-control form-control-sm mr-2" id="file" wire:model="file">
                                    @error('file') <span class="text-danger small">{{ $message }}</span> @enderror
                                    <button type="submit" class="btn btn-success btn-sm bi bi-cloud-upload-fill"></button>
                                </div>
                            </form>
							<button wire:click="downloadAirlines" class="btn btn-warning btn-sm bi bi-cloud-download-fill"> Download Sample</button>
                        </div>
						<div class="btn btn-sm btn-info bi bi-plus-lg" data-bs-toggle="modal" data-bs-target="#dataModal">
							 Add Airline
						</div>
					</div>
				</div>
				<div class="card-body">
					@include('livewire.airlines.modals')
					<div class="row">
						@forelse($airlines as $row)
						<div class="col-md-4 border d-flex justify-content-between">
							<div class="p-2">
								<b><i class="bi bi-building-check text-success"></i> {{ $row->name }} - {{ $row->iata_code }}</b>
								<p> <i class="bi bi-house-gear text-info"> </i> {{ $row->base }}</p>
							</div>
							<div>
								<div class="dropdown p-2">
									<a class="btn custom-btn-sm text-white btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
										Actions
									</a>
									<ul class="dropdown-menu">
										<li><a data-bs-toggle="modal" data-bs-target="#dataModal" class="dropdown-item bi bi-pencil-square" wire:click="edit({{$row->id}})"> Edit </a></li>
										<li><a class="dropdown-item bi bi-trash3" onclick="confirm('Confirm Delete Airline id {{$row->id}}? \nDeleted Airline cannot be recovered!')||event.stopImmediatePropagation()" wire:click="destroy({{$row->id}})"> Delete </a></li>  
									</ul>
								</div>
							</div>
						</div>
						@empty
						<div class="col-md-12">
							<h5 class="text-center">No Airlines Created Yet</h5>
						</div>
						@endforelse
					</div>
					<div class="float-end mt-2 mb-0">{{ $airlines->links() }}</div>
				</div>
			</div>
		</div>
	</div>
</div>