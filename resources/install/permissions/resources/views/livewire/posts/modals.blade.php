<!-- Create / Edit Post Modal -->
<div wire:ignore.self class="modal fade" id="dataModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel">
                    {{ $post_id ? 'Edit Post' : 'Create New Post' }}  
                </h5>
                <button wire:click.prevent="cancel()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" wire:model.lazy"title">
                        @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="body">Body</label>
                        <textarea class="form-control" id="body" rows="5" wire:model.lazy"body"></textarea>
                        @error('body') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="photo">Photo</label>
                        <input type="file" class="form-control" name="photo" id="photo" wire:model.lazy"photo">
                        @error('photo') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex align-items-center justify-content-between">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary" wire:click.prevent="store">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- View Post Modal -->
<div wire:ignore.self class="modal fade" id="viewModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">View Post</h5>
                <button wire:click.prevent="cancel()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="col-12 bg-white px-3 mb-3 pb-3">
                    @if ($selectedPostId)
                        <h5 class="border-bottom">Post Title: {{ $selectedPost->title }}</h5>
                        <p class="border-bottom mb-2">Body: {{ $selectedPost->body }}</p>
                        @if ($selectedPost->photo)
                            <img src="{{ asset('storage/' . $selectedPost->photo) }}" alt="{{ $selectedPost->title }}" class="img-fluid mt-2">
                        @endif
                    @else
                        <p>No post selected.</p>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" wire:click.prevent="cancel()" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>