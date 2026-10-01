@props(['type' => 'success', 'message'])

<div class="toast align-items-center text-bg-{{ $type }} border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="4000">
    <div class="d-flex">
        <div class="toast-body">
            <i class="bi {{ $type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle' }} me-2"></i>{{ $message }}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
    </div>
</div>
