@extends('layouts.app')

@section('title', 'Laporan Saya')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('content')
    <x-breadcrumb :items="['Laporan Saya']" />

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold mb-0">Laporan Saya</h4>
        <a href="{{ route('lapor.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Buat Laporan
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table id="tabelLaporanSaya" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>Nomor Laporan</th>
                        <th>Gedung / Lokasi</th>
                        <th>Status</th>
                        <th>SLA</th>
                        <th>Tanggal</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script>
$(function () {
    $('#tabelLaporanSaya').DataTable({
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: '{{ route('lapor.index') }}',
        order: [[4, 'desc']],
        columns: [
            { data: 'nomor_laporan', name: 'nomor_laporan' },
            { data: 'gedung', name: 'gedung.nama_gedung' },
            { data: 'status', name: 'status.nama' },
            { data: 'sla', name: 'prioritas.nama', orderable: false },
            { data: 'tanggal', name: 'created_at' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-end' },
        ],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
    });
});
</script>
@endpush
