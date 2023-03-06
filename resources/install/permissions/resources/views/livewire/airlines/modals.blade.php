<!-- Create / Edit Airlines Modal -->
<div wire:ignore.self class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel">
                    Create New Registration
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="name">Airline Name</label>
                        <input type="text" class="form-control" id="name" wire:model.lazy="name">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="iata_code">Airline Code</label>
                        <input type="text" class="form-control" id="iata_code" wire:model.lazy="iata_code">
                        @error('iata_code') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="base">Airline Base</label>
                        <input type="text" class="form-control" id="base" wire:model.lazy="base">
                        @error('base') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex align-items-center justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button wire:click.prevent="saveAirline" type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>