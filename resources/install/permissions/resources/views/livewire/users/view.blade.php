@section('title', 'Users')
<div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4>@if($mode == 'add') Add User @elseif($mode == 'edit') Edit User @else() Users @endif()</h4>
                            @if (session()->has('message'))
                                <div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
                            @endif
                            <button class="btn btn-sm btn-primary float-end align-items-center"  
                                @if($form) wire:click="cancel()" @else() wire:click="newUser()" @endif()>
                                @if($form) <i class="bi bi-backspace-fill"></i> @else() <i class="bi bi-person-add"></i> @endif()
                                {{$form ? 'All Users' : 'Add User'}}
                            </button>
                        </div>
                    </div>
                    @if($form)
                    <form wire:submit.prevent="submit">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6" wire:target="editUser, newUser">
                                    <div class="col-md-12 mb-3">
                                        <label for="name">Name</label>
                                        <input type="text" wire:model.lazy="name" placeholder="Name" class="form-control">
                                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>                                                
                                    <div class="col-md-12 mb-3">
                                        <label for="email">Email</label>
                                        <input type="email" wire:model.lazy="email" placeholder="Email" class="form-control">
                                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div> 
                                    <div class="col-md-12 mb-3">
                                        <label for="phone">Phone</label>
                                        <input type="text" wire:model.lazy="phone" placeholder="Phone" class="form-control">
                                        @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div> 
                                    <div class="col-md-12 mb-3">
                                        <label for="title">Title</label>
                                        <input type="text" wire:model.lazy="title" placeholder="Title" class="form-control">
                                        @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="photo">Photo</label>
                                        <input type="file" wire:model.lazy="photo" placeholder="Photo" class="form-control">
                                        @error('photo') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="password">Password</label>
                                        <input type="password" wire:model.lazy="password" placeholder="Password" class="form-control">
                                        @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="password_confirmation">Confirm Password</label>
                                        <input type="password" wire:model.lazy="password_confirmation" placeholder="Confirm Password" class="form-control">
                                        @error('password_confirmation') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div  class="col-md-12 mb-3">
                                        <label class="h5">Assin Roles</label>
                                        <table class="table table-sm table-bordered">
                                            <tbody>
                                                @foreach($roles as $role)
                                                <tr>
                                                    <td class="text-center" width="40">
                                                        <input type="checkbox" wire:model.lazy"selectedRoles" value="{{ $role->id }}" class="form-check-input"
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
                                <div class="mt-3 col-md-6 d-flex align-items-center justify-content-between">
                                    <button wire:click="cancel()" class="btn btn-primary">Back</button>
                                    <button type="submit" class="btn btn-primary">{{$mode === 'add' ? 'Add User' : 'Save Changes'}}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    @else()
                    <div class="card-body">
                        @include('livewire.users.modals')
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Title</th>
                                        <th>Roles</th>
                                        <th>Picture</th>
                                        <th width="220">Actions</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->title }}</td>
                                        <td>
                                            @foreach($user->roles as $role)
                                                <button class="btn btn-sm btn-warning bi bi-shield-shaded"> {{ $role->name }}</button>
                                            @endforeach
                                        </td>
                                        <td class="text-center"><img class="profile-img" src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->title }}"></td>
                                        <td class="text-center">
                                            <button data-bs-toggle="modal" data-bs-target="#viewModal" class="btn btn-sm btn-info text-white bi bi-eye" wire:click="viewUser({{ $user->id }})"> View</button>
                                            <button class="btn btn-sm btn-primary text-dark bi bi-pencil-square" wire:click="edit({{ $user->id }})"> Edit</button>
                                            <button class="btn btn-sm btn-danger text-white bi bi-trash3-fill" 
                                            wire:click="destroy({{ $user->id }})" 
                                            onclick="confirm('Confirm Delete \'{{ $user->name }} - {{ $user->email }}\'? \n\nDeleted Users cannot be recovered!')||event.stopImmediatePropagation()"> Delete</button>
                                        <td>
                                            {{$user->created_at->format('d-M-Y H:i:s')}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="float-end">{{ $users->links() }}</div>
                    </div>
                    @endif()
                </div>
            </div>
        </div>
    </div>
</div>