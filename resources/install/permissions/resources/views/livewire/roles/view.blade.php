<div>
	@section('title', 'Roles')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            @if($form)<h4>{{ $role->id ? 'Edit' : 'Create' }} Role </h4> @else <h4>Roles</h4> @endif
                            @if (session()->has('message'))
                                <div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
                            @endif
                            <button class="btn btn-sm btn-primary float-right" wire:click="form()">
                                <i class="bi bi-plus-circle"> </i> Add Role
                            </button>
                        </div>
                    </div>
                @if($form)
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row g-2 mb-3">
                                    <div class="col-md-12">
                                        <label class="h4" for="role.name">Role Name <span class="text-danger small">*</span></label>
                                        <input type="text" wire:model.lazy="role.name" class="form-control">
                                        @error('role.name') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mt-3" style="display: flex; justify-content: space-between; align-items: center;">
                                        <button class="btn btn-sm btn-info" wire:click="index()"><i class="bi bi-x-circle"></i> Cancel</button>
                                        <button class="btn btn-sm btn-success" wire:click="save()"><i class="bi bi-check-circle"></i> Save Role</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-md-6">
                                <h4 class="">Assign Permissions</h4>
                                <table class="table table-sm table-bordered">
                                    <tbody>
                                        @foreach($permissions as $permission)
                                        <tr>
                                            <td width="40">
                                                <input class="form-check-input" type="checkbox" 
                                                    wire:model.lazy="permissions_selection" 
                                                    value="{{ $permission->id }}" 
                                                    id="permission_{{ $permission->id }}">
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                    <strong>{{ $permission->name }}</strong>
                                                </label>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else()
                    <div class="card-body">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Permissions</th>
                                    <th width="40"></th>
                                    <th width="40"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        @foreach($role->permissions as $permission)
                                            <button class="btn btn-sm btn-warning mb-1"><i class="bi bi-shield-shaded"></i> {{ $permission->name }}</button>
                                        @endforeach
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" wire:click="form({{$role}})"><i class="bi bi-pencil-square"></i></button>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" 
                                            onclick="confirm('Are you shure want to delete role: {{ $role->name }}?') || event.stopImmediatePropagation()" 
                                            wire:click="delete({{ $role }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="float-end">{{ $roles->links() }}</div>
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>