@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <h5 class="text-center mb-4 fw-semibold">Masuk ke Akun Anda</h5>

    @if (session('status'))
        <div class="alert alert-success small">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label small fw-medium">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label small fw-medium">Kata Sandi</label>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label small" for="remember">Ingat saya</label>
            </div>
            <a href="{{ route('password.request') }}" class="small text-decoration-none">Lupa kata sandi?</a>
        </div>

        <button type="submit" class="btn btn-primary w-100">Masuk</button>
    </form>
@endsection
