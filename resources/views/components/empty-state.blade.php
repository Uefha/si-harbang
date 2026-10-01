@props(['icon' => 'bi-inbox', 'title' => 'Belum ada data', 'description' => null])

<div class="text-center text-secondary py-5">
    <i class="bi {{ $icon }}" style="font-size: 2.5rem; opacity: .4"></i>
    <p class="mt-3 mb-0 fw-medium">{{ $title }}</p>
    @if ($description)
        <p class="small mb-0">{{ $description }}</p>
    @endif
</div>
