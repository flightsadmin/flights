<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Posts extends Component
{
    use WithPagination, WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    public $title, $body, $photo, $keyWord, $post_id, $selectedPostId;

    protected $rules = [
        'title' => 'required',
        'body'  => 'required',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ];

    public function cancel()
    {
        $this->reset();
    }

    public function store()
    {
        $this->validate();

        if ($this->post_id) {
            $post = Post::findOrFail($this->post_id);
            if ($post->photo) {
                Storage::disk('public')->delete($post->photo);
            }
        }
        
        $path = $this->photo ? $this->photo->store('posts', 'public') : null;

        Post::updateOrCreate(['id' => $this->post_id], [
            'title' => $this->title,
            'body'  => $this->body,
            'photo' => $path,
        ]);

        $this->reset();
        $this->dispatchBrowserEvent('closeModal');
        session()->flash('message', $this->post_id ? 'Post Updated Successfully.' : 'Post Created Successfully.');
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $this->post_id = $id;
        $this->title = $post->title;
        $this->body = $post->body;
        $this->photo = $post->photo;
    }

    public function destroy($id)
    {
        Post::find($id)->delete();
        session()->flash('message', 'Post Deleted Successfully.');
    }

    public function viewPost($post_id)
    {
        $this->selectedPostId = $post_id;
    }

    public function getSelectedPostProperty()
    {
        return $this->selectedPostId ? Post::findOrFail($this->selectedPostId) : null;
    }

    public function render()
    {
        $keyWord = '%'. $this->keyWord .'%';
        $posts = Post::latest()
                    ->orWhere('title', 'LIKE', $keyWord)
                    ->orWhere('body', 'LIKE', $keyWord)
                    ->paginate(10);
        return view('livewire.posts.view', [
            'posts' => $posts,
            'selectedPost' => $this->selectedPostId ? Post::findOrFail($this->selectedPostId) : null,
        ]);
    }
}