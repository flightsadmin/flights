<!-- View User Modal -->
<div wire:ignore.self class="modal fade" id="viewModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Profile</h5>
                <button wire:click.prevent="cancel()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if ($selectedUserId)
                <div class="col-12 bg-white px-3 mb-3 pb-3">
                    <div class="d-flex flex-column align-items-center border-bottom">
                        <img class="profile-img mb-2" src="{{ asset('storage/' . $selectedUser->photo) }}"  style="height:100px; width:100px;"  alt="{{ $selectedUser->title }}">
                    </div>
                    <div class="d-flex align-items-center justify-content-between border-bottom">
                        <p class="py-2"><i class="bi bi-person-vcard-fill"></i> Full Name</p>
                        <p class="py-2 text-muted">{{ $selectedUser->name }}</p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between border-bottom">
                        <p class="py-2"><i class="bi bi-envelope-at-fill"></i> Email</p>
                        <p class="py-2 text-muted"> {{ $selectedUser->email }}</p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between border-bottom">
                        <p class="py-2"><i class="bi bi-telephone-fill"></i> Phone</p>
                        <p class="py-2 text-muted">{{ $selectedUser->phone }}</p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between border-bottom">
                        <p class="py-2"><i class="bi bi-geo-alt-fill"></i> Title</p>
                        <p class="py-2 text-muted">{{ $selectedUser->title }}</p>
                    </div>
                </div>
                @else
                    <p>No Users selected.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" wire:click.prevent="cancel()" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>