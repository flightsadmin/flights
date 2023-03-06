<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class Users extends Component
{
    use WithPagination, WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    public $userId, $name, $email, $photo, $phone, $title, $password, $password_confirmation, $mode, $form = false, $selectedRoles = [], $user, $selectedUserId;

    public function mount($userId = null)
    {
        if ($userId) {
            $this->user = User::find($userId);
        } else {
            $this->user = new User;
        }
    }

    protected function rules()
    {
        return [
            'name'          => 'required|min:6',
            'email'         => 'required|email',
            'password'      => 'required|confirmed',
            'phone'         => 'nullable|min:9|numeric',
            'photo'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'         => 'nullable|min:6',
            'selectedRoles' => 'required',
        ];
    }

    public function cancel()
    {
        $this->resetErrorBag();
        $this->form = false;
    }

    public function newUser()
    {
        $this->reset();
        $this->mode = 'add';
        $this->form = true;
    }

    public function edit($userId)
    {
        $this->reset();
        $this->userId = $userId;
        $user = User::findOrFail($userId);
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->photo = $user->photo;
        $this->title = $user->title;
        $this->selectedRoles = $user->roles->pluck('id')->toArray();
        $this->mode = 'edit';
        $this->form = true;
    }

    public function viewUser($userId)
    {
        $this->selectedUserId = $userId;
    }

    public function getSelectedUserProperty()
    {
        return $this->selectedUserId ? User::findOrFail($this->selectedUserId) : null;
    }

    public function submit()
    {
        $this->validate();
        if($this->mode === 'add'){
            $user = new User();
        }else {
            $user = User::find($this->userId);
            if($user->photo){
                Storage::disk('public')->delete($user->photo);
            }
        }
        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone = $this->phone;
        if ($this->photo) {
            $photo  = $this->photo->store('users', 'public');
            $user->photo = $photo;
        }
        $user->title = $this->title;
        $user->password = Hash::make($this->password);
        $user->save();
        $user->syncRoles($this->selectedRoles);

        if($this->mode === 'add'){
            $emailData = [
                'name'      => $this->name,
                'email'     => $this->email,
                'phone'     => $this->phone,
                'password'  => $this->password
            ];
            Mail::send('mails.email', $emailData, function($message) use($emailData) {
                $message->to($emailData['email'], $emailData['name'])
                        ->subject('New Account for '. $emailData['name']);
            });
        }
        $this->reset();
        $this->form = false;
        session()->flash('message', 'User successfully updated.');
    }

    public function destroy($userId)
    {
        $user = User::findOrFail($userId);
        Storage::disk('public')->delete($user->photo);
        $user->delete();
    }

    public function render()
    {
        $roles = Role::with('permissions')->get();
        return view('livewire.users.view', [
            'users' => User::latest()->paginate(10),
            'roles' => $roles,
            'selectedUser' => $this->selectedUserId ? User::findOrFail($this->selectedUserId) : null,
        ]);
    }
}