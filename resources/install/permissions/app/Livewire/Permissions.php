<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Permissions extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $form = false;
    public Permission $permission;

    protected function rules()
    {
        return [
            'permission.name'        => 'required|min:3|max:100',
            'permission.guard_name'  => 'required',
        ];
    }
    
    public function index()
    {
        $this->resetErrorBag();
        $this->form = false;
    }

    public function form(Permission $permission)
    {
        $this->permission = Permission::firstOrNew([
            'name' => $permission->name, 
            'guard_name' => config('auth.defaults.guard')
        ]);
        $this->form = true;
    }

    public function save()
    {
        $this->validate();
        $this->permission->save();
        $this->dispatch('closeModal');
        session()->flash('message', 'Permission created successfully.');
        $this->index();
    }

    public function delete(Permission $permission)
    {
        $permission->delete();
    }

    public function render()
    {
        $permissions = Permission::with('roles')->paginate();
        return view('livewire.permissions.view', [
            'permissions' => $permissions,
        ]);
    }
}