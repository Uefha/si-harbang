@extends('layouts.app')

@section('title', 'Riwayat Aktivitas')

@section('content')
    <x-breadcrumb :items="['Riwayat Aktivitas']" />

    <h4 class="fw-semibold mb-3">Riwayat Aktivitas</h4>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table id="tabelAktivitas" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aktivitas</th>
                        <th>Laporan Terkait</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script>
$(function () {
    $('#tabelAktivitas').DataTable({
        processing: true,
        responsive: true,
        serverSide: true,
        order: [[0, 'desc']],
        ajax: "{{ route('admin.aktivitas-log') }}",
        columns: [
            { data: 'waktu', name: 'created_at' },
            { data: 'user', name: 'user.name' },
            { data: 'aktivitas', name: 'aktivitas' },
            { data: 'laporan', name: 'laporan.nomor_laporan' },
            { data: 'ip_address', name: 'ip_address', defaultContent: '-' },
        ],
    });
});
</script>
@endpush
