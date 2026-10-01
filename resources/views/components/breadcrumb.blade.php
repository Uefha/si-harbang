@props(['items' => []])

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
        @foreach ($items as $label => $url)
            @if (is_numeric($label))
                <li class="breadcrumb-item active" aria-current="page">{{ $url }}</li>
            @elseif ($loop->last)
                <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
            @else
                <li class="breadcrumb-item"><a href="{{ $url }}" class="text-decoration-none">{{ $label }}</a></li>
            @endif
        @endforeach
    </ol>
</nav>
