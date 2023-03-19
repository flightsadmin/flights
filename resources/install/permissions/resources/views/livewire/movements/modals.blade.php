<!-- Create / Edit Flight Modal -->
<div wire:ignore.self class="modal fade" id="dataModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel">
                    Send Movements Message
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="grid gap-3">
                    <div class="form-group p-2 g-col-6">
                        <label for="flight_id">Flight</label>
                        <select class="form-select" id="flight_id" wire:model="flight_id">
                            <option value="">-- Select Flight --</option>
                            @foreach ($flights as $flight)
                                <option value="{{ $flight->id }}">{{ $flight->flight_no }}</option>
                            @endforeach
                        </select>
                        @error('flight_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group p-2 g-col-6">
                        <label for="touchdown">Touchdown</label>
                        <input type="datetime-local" class="form-control" id="touchdown" wire:model="touchdown">
                        @error('touchdown') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group p-2 g-col-6">
                        <label for="onblocks">On Blocks</label>
                        <input type="datetime-local" class="form-control" id="onblocks" wire:model="onblocks">
                        @error('onblocks') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group p-2 g-col-6">
                        <label for="offblocks">Off Blocks</label>
                        <input type="datetime-local" class="form-control" id="offblocks" wire:model="offblocks">
                        @error('offblocks') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group p-2 g-col-6">
                        <label for="airborne">Airborne</label>
                        <input type="datetime-local" class="form-control" id="airborne" wire:model="airborne">
                        @error('airborne') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group p-2 g-col-6">
                        <label for="passengers">Passengers</label>
                        <input type="number" class="form-control" id="passengers" wire:model="passengers">
                        @error('passengers') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group p-2 g-col-6">
                        <label for="remarks">Remarks</label>
                        <textarea class="form-control" id="remarks" wire:model="remarks"></textarea>
                        @error('remarks') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button wire:click.prevent="save" type="button" class="btn btn-primary">Save</button>
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