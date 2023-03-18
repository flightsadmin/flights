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
                        <p class="py-2 bi bi-person-vcard-fill"> Full Name</p>
                        <p class="py-2 text-muted">{{ $selectedUser->name }}</p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between border-bottom">
                        <p class="py-2 bi bi-envelope-at-fill"> Email</p>
                        <p class="py-2 text-muted"> {{ $selectedUser->email }}</p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between border-bottom">
                        <p class="py-2 bi bi-telephone-fill"> Phone</p>
                        <p class="py-2 text-muted">{{ $selectedUser->phone }}</p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between border-bottom">
                        <p class="py-2 bi bi-geo-alt-fill"> Title</p>
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

<!-- Add / Edit User Modal -->
<div wire:ignore.self class="modal fade" id="dataModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><h4>{{ $userId ? 'Edit User' : 'Create New User' }}</h4></h5>
                <button wire:click="cancel()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name">Name</label>
                            <input type="text" wire:model.lazy="name" placeholder="Name" class="form-control">
                            @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>                                                
                        <div class="col-md-6 mb-3">
                            <label for="email">Email</label>
                            <input type="email" wire:model.lazy="email" placeholder="Email" class="form-control">
                            @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div> 
                        <div class="col-md-6 mb-3">
                            <label for="phone">Phone</label>
                            <input type="text" wire:model.lazy="phone" placeholder="Phone" class="form-control">
                            @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div> 
                        <div class="col-md-6 mb-3">
                            <label for="title">Title</label>
                            <input type="text" wire:model.lazy="title" placeholder="Title" class="form-control">
                            @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="photo">Photo</label>
                            <input type="file" wire:model="photo" placeholder="Photo" class="form-control">
                            @error('photo') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password">Password</label>
                            <input type="password" wire:model.lazy="password" placeholder="Password" class="form-control">
                            @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" wire:model.lazy="password_confirmation" placeholder="Confirm Password" class="form-control">
                            @error('password_confirmation') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        @can('editUser')
                        <div class="col-md-12">
                            <div class="col-md-12 mb-3">
                                <label class="h5">Assin Roles</label>
                                <table class="table table-sm table-bordered">
                                    <tbody>
                                        @foreach($roles as $role)
                                        <tr>
                                            <td class="text-center" width="40">
                                                <input type="checkbox" wire:model.lazy="selectedRoles" value="{{ $role->id }}" class="form-check-input"
                                                @checked(in_array($role->id, $selectedRoles))
                                            </td>
                                            <td>
                                                <label class="form-check-label">{{ $role->name }}</label>
                                            </td>
                                        </tr>
                                        @endforeach
                                            @error('selectedRoles') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endcan
                    </div>
                </form>            
            </div>
            <div class="modal-footer mt-3 d-flex align-items-center justify-content-between">
                <button wire:click="cancel()" data-bs-dismiss="modal" class="btn btn-primary">Back</button>
                <button wire:click.prevent="submit()" class="btn btn-primary">{{ $userId ? 'Edit Changes' : 'Add User'}}</button>
            </div>
        </div>
    </div>
</div>