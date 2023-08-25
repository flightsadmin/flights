@extends('components.layouts.app')
@section('title', __('Flight'))
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @livewire('flights')
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="module">
    const modal = new bootstrap.Modal('#dataModal');
    const mvtmodal = new bootstrap.Modal('#mvtModal');
    const servicemodal = new bootstrap.Modal('#viewModal');
    window.addEventListener('closeModal', () => {
        modal.hide();
        mvtmodal.hide();
        servicemodal.hide();
    });

    const toast = new bootstrap.Toast('#statusToast');
    window.addEventListener('closeModal', () => {
        toast.show();
    });
</script>
@endpush