@extends('layouts.app')

@section('title', $laporan->nomor_laporan)

@section('content')
    <h4 class="fw-semibold mb-3">Detail Laporan Saya</h4>

    <div class="row g-3">
        <div class="col-lg-8">
            @include('laporan._detail_main')
        </div>
        <div class="col-lg-4">
            @include('laporan._detail_sidebar')
        </div>
    </div>
@endsection
