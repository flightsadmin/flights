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
    public $userId, $name, $email, $photo, $phone, $title, $password, $password_confirmation, $selectedRoles = [], $selectedUserId;

    public function render()
    {
        $roles = Role::with('permissions')->get();
        return view('livewire.users.view', [
            'users' => User::latest()->paginate(10),
            'roles' => $roles,
            'selectedUser' => $this->selectedUserId ? User::findOrFail($this->selectedUserId) : null,
        ]);
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

    public function submit()
    {
        $this->validate();
        $user = User::findOrFail($this->userId);
        if($user->photo){
            Storage::disk('public')->delete($user->photo);
        }
        if ($this->photo) {
            $photo  = $this->photo->store('users', 'public');
        }
        $user = User::updateOrCreate(['id' => $this->userId], 
        [
            'name'      => $this->name,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'title'     => $this->title,
            'photo'     => $this->photo ? $photo : 'image.png',
            'password'  => Hash::make($this->password),
        ]);

        $user->syncRoles($this->selectedRoles);

        $this->dispatchBrowserEvent('closeModal');
        $this->reset();
        session()->flash('message', $this->userId ? 'User Updated Successfully.' : 'User Created Successfully.');

        // if($this->mode === 'add'){
        // $emailData = [
        //     'name'      => $this->name,
        //     'email'     => $this->email,
        //     'phone'     => $this->phone,
        //     'password'  => $this->password
        // ];
        // Mail::send('mails.email', $emailData, function($message) use($emailData) {
        //     $message->to($emailData['email'], $emailData['name'])
        //             ->subject('New Account for '. $emailData['name']);
        // });
        // }
    
    }

    public function viewUser($userId)
    {
        $this->selectedUserId = $userId;
    }

    public function getSelectedUserProperty()
    {
        return $this->selectedUserId ? User::findOrFail($this->selectedUserId) : null;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->photo = $user->photo;
        $this->title = $user->title;
        $this->selectedRoles = $user->roles->pluck('id')->toArray();
    }

    public function destroy($userId)
    {
        $user = User::findOrFail($userId);
        Storage::disk('public')->delete($user->photo);
        $user->delete();
    }

    public function cancel()
    {
        $this->resetErrorBag();
        $this->reset();
    }
}