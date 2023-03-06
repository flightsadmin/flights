<div>
	@section('title', 'Permissions')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            @if($form)<h4>{{ $permission->id ? 'Edit' : 'Create' }} Permission </h4> @else <h4>Permissions</h4> @endif
                            @if (session()->has('message'))
                                <div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
                            @endif
                            <button class="btn btn-sm btn-primary float-end bi bi-plus-circle" wire:click="form()">
                                Add Permission
                            </button>
                        </div>
                    </div>

                    @if($form)
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row g-2 mb-3">
                                    <div class="col-md-12">
                                    <label class="h5" for="permission.name">Permissions Name <span class="text-danger small">*</span></label>
                                        <input type="text" wire:model.lazy="permission.name" class="form-control">
                                        @error('permission.name') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mt-3" style="display: flex; justify-content: space-between; align-items: center;">
                                        <button class="btn btn-sm btn-info bi bi-x-circle" wire:click="index()"> Cancel</button>
                                        <button class="btn btn-sm btn-success bi bi-check-circle" wire:click="save()"> Save Permission</button>
                                    </div>
                                </div>
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
                                    <th>Roles</th>
                                    <th width="40"></th>
                                    <th width="40"></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($permissions as $permission)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $permission->name }}</td>
                                    <td>
                                        @foreach($permission->roles as $role)
                                            <button class="btn btn-sm btn-warning bi bi-shield-shaded"> {{ $role->name }}</button>
                                        @endforeach
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary bi bi-pencil-square" wire:click="form({{$permission}})"> </button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger bi bi-trash" 
                                            onclick="confirm('Are you shure want to delete permision: {{ $permission->name }}?') || event.stopImmediatePropagation()" 
                                            wire:click="delete({{$permission}})">
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="float-end">{{ $permissions->links() }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>