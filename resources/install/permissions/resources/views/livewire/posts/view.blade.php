@section('title', __('Posts'))
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div style="display: flex; justify-content: space-between; align-items: center;">
						<div class="float-left">
							<h4>Posts </h4>
						</div>
						@if (session()->has('message'))
						<div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
						@endif
						<div>
							<input wire:model.debounce.500ms="keyWord" type="text" class="form-control form-control-sm" name="search" id="search" placeholder="Search Posts">
						</div>
						<div class="btn btn-sm btn-info bi bi-plus-lg" data-bs-toggle="modal" data-bs-target="#dataModal">
							Add Posts
						</div>
					</div>
				</div>
				
				<div class="card-body">
						@include('livewire.posts.modals')
				<div class="table-responsive">
					<table class="table table-bordered table-sm">
						<thead class="thead">
							<tr> 
								<td>#</td> 
								<th>Title</th>
								<th>Body</th>
								<th>Photo</th>
								<td>ACTIONS</td>
							</tr>
						</thead>
						<tbody>
							@forelse($posts as $row)
							<tr>
								<td>{{ $loop->iteration }}</td> 
								<td>{{ $row->title }}</td>
								<td>{{ $row->body }}</td>
								<td width="90"><img class="profile-img" src="{{ asset('storage/' . $row->photo) }}" alt="{{ $row->title }}"></td>
								<td width="90">
									<div class="dropdown">
										<a class="btn btn-sm btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
											Actions
										</a>
										<ul class="dropdown-menu">
											<li><a data-bs-toggle="modal" data-bs-target="#viewModal" class="dropdown-item bi bi-eye" wire:click="viewPost({{ $row->id }})" > View </a></li>
											<li><a data-bs-toggle="modal" data-bs-target="#dataModal" class="dropdown-item bi bi-pencil-square" wire:click="edit({{$row->id}})"> Edit </a></li>
											<li><a class="dropdown-item bi bi-trash3" onclick="confirm('Confirm Delete Post id {{$row->id}}? \nDeleted Posts cannot be recovered!')||event.stopImmediatePropagation()" wire:click="destroy({{$row->id}})"> Delete </a></li>  
										</ul>
									</div>								
								</td>
							</tr>
							@empty
							<tr>
								<td class="text-center" colspan="100%">No data Found </td>
							</tr>
							@endforelse
						</tbody>
					</table>						
					<div class="float-end">{{ $posts->links() }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>