<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div style="display: flex; justify-content: space-between; align-items: center;">
						<div class="float-left">
							<h4>Airlines Routes and Addresses</h4>
						</div>
						<div class="btn btn-sm btn-info bi bi-plus-lg" data-bs-toggle="modal" data-bs-target="#dataModal">
							 Add Route
						</div>
					</div>
				</div>
				<div class="card-body">
					@include('livewire.routes.modals')
					<div class="row">
						@forelse($airlines as $airline)
							<div class="col-md-4 border p-2">
								<p><b>{{ $airline->name }}</b></p>
								<ol>
									@foreach($airline->routes as $route)
										<li>{{ $route->origin }} - {{ $route->destination }}</li>
											@foreach($route->emails as $email)
												<ul><small>{{ $email->email }}</small></ul>
											@endforeach
									@endforeach
								</ol>
							</div>
						@empty
							<h3 class="text-center">No Airlines Found </h3>
						@endforelse
					</div>
				</div>
			</div>
		</div>
	</div>
</div>