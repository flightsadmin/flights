@extends('layouts.app')
@section('title', __('Routes'))
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @livewire('routes')
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="module">
    const modal = new bootstrap.Modal('#dataModal');
    window.addEventListener('closeModal', () => {
        modal.hide();
    });

    const toast = new bootstrap.Toast('#statusToast');
    window.addEventListener('closeModal', () => {
        toast.show();
    });
</script>
@endpush