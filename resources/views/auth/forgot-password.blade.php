@extends('layouts.guest')

@section('title', 'Lupa Kata Sandi')

@php
    $kontakNama = \App\Models\Pengaturan::get(\App\Models\Pengaturan::KONTAK_ADMIN_NAMA, 'Admin');
    $kontakTelepon = \App\Models\Pengaturan::get(\App\Models\Pengaturan::KONTAK_ADMIN_TELEPON);
    $kontakPesan = \App\Models\Pengaturan::get(\App\Models\Pengaturan::KONTAK_ADMIN_PESAN);
    $nomorWa = \App\Models\Pengaturan::nomorWhatsApp();
@endphp

@section('content')
    <h5 class="mb-3 fw-semibold">Lupa Kata Sandi</h5>

    <p class="small text-secondary">
        {{ $kontakPesan ?? 'Untuk keamanan, kata sandi hanya dapat diatur ulang oleh admin. Silakan hubungi kontak di bawah ini.' }}
    </p>

    <div class="border rounded-3 p-3 mb-3 bg-light">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;">
                <i class="bi bi-person-badge text-primary fs-5"></i>
            </div>
            <div>
                <div class="fw-semibold">{{ $kontakNama }}</div>
                @if ($kontakTelepon)
                    <div class="text-secondary small">{{ $kontakTelepon }}</div>
                @endif
            </div>
        </div>

        @if ($kontakTelepon)
            <div class="d-flex gap-2 mt-3">
                @if ($nomorWa)
                    <a href="https://wa.me/{{ $nomorWa }}?text={{ urlencode('Halo, saya lupa kata sandi akun SI-HARBANG saya. Mohon bantuannya.') }}"
                       target="_blank" rel="noopener" class="btn btn-success btn-sm flex-fill">
                        <i class="bi bi-whatsapp me-1"></i>WhatsApp
                    </a>
                @endif
                <a href="tel:{{ $kontakTelepon }}" class="btn btn-outline-secondary btn-sm flex-fill">
                    <i class="bi bi-telephone me-1"></i>Telepon
                </a>
            </div>
        @endif
    </div>

    <a href="{{ route('login') }}" class="btn btn-link w-100">Kembali ke halaman masuk</a>
@endsection
