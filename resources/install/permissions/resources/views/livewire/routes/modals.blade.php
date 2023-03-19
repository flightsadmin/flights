<!-- Create / Edit Airlines Modal -->
<div wire:ignore.self class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel">
                    Airline Routes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>
                    <form>
                        <div class="form-group">
                            <label for="airline">Airline:</label>
                            <select class="form-select" wire:model="airlineId">
                                <option value="">Select an airline</option>
                                @foreach ($airlines as $airline)
                                    <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                                @endforeach
                            </select>
                            @error('airlineId') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="origin">Origin:</label>
                            <input type="text" class="form-control" wire:model="origin">
                            @error('origin') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="destination">Destination:</label>
                            <input type="text" class="form-control" wire:model="destination">
                            @error('destination') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="emails">Emails:</label>
                            <div class="d-flex gap-3">
                                <input type="text" class="form-control" wire:model="email" placeholder="test@test.com.">
                                <button wire:click.prevent="addEmail('{{ $email }}')" class="btn btn-sm btn-secondary bi bi-plus"></button>
                            </div>
                            <ol class="mt-2">
                                @foreach ($emails as $email)
                                    <li>{{ $email }} <a href="#" wire:click.prevent="removeEmail('{{ $email }}')" class="bi bi-trash"></a></li>
                                @endforeach
                            </ol>
                            @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                    </form>
                </div>
            </div>
            <div class="modal-footer d-flex align-items-center justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" wire:click.prevent="save">Save</span>
                </button>
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