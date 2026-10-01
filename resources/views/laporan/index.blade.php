@extends('layouts.app')

@section('title', 'Semua Laporan')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('content')
    <x-breadcrumb :items="['Semua Laporan']" />
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h4 class="fw-semibold mb-0">Semua Laporan Kerusakan</h4>
        <div class="d-flex gap-2">
            <a href="#" id="btnExportExcel" class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
            </a>
            <a href="#" id="btnExportPdf" class="btn btn-outline-danger btn-sm" target="_blank">
                <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-medium mb-1">Dari Tanggal</label>
                    <input type="date" id="filterTanggalDari" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-medium mb-1">Sampai Tanggal</label>
                    <input type="date" id="filterTanggalSampai" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-medium mb-1">Status</label>
                    <select id="filterStatus" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach ($statusList as $s)
                            <option value="{{ $s->id }}">{{ $s->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-medium mb-1">Prioritas</label>
                    <select id="filterPrioritas" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach ($prioritasList as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-medium mb-1">Gedung</label>
                    <select id="filterGedung" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach ($gedungList as $g)
                            <option value="{{ $g->id }}">{{ $g->nama_gedung }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-medium mb-1">Jenis Kerusakan</label>
                    <select id="filterJenis" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach ($jenisKerusakanList as $j)
                            <option value="{{ $j->id }}">{{ $j->nama_jenis }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-medium mb-1">Petugas Harbang</label>
                    <select id="filterPetugas" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach ($petugasList as $pt)
                            <option value="{{ $pt->id }}">{{ $pt->user?->name ?? '(akun tidak ditemukan)' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-light btn-sm w-100" id="btnResetFilter">
                        <i class="bi bi-x-circle me-1"></i>Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table id="tabelLaporan" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>Nomor Laporan</th>
                        <th>Pelapor</th>
                        <th>Gedung / Lokasi</th>
                        <th>Jenis</th>
                        <th>Petugas</th>
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
    const table = $('#tabelLaporan').DataTable({
        processing: true,
        responsive: true,
        serverSide: true,
        order: [[7, 'desc']],
        ajax: {
            url: '{{ route('laporan.index') }}',
            data: function (d) {
                d.status_id = $('#filterStatus').val();
                d.prioritas_id = $('#filterPrioritas').val();
                d.gedung_id = $('#filterGedung').val();
                d.jenis_kerusakan_id = $('#filterJenis').val();
                d.petugas_harbang_id = $('#filterPetugas').val();
                d.tanggal_dari = $('#filterTanggalDari').val();
                d.tanggal_sampai = $('#filterTanggalSampai').val();
            }
        },
        columns: [
            { data: 'nomor_laporan', name: 'nomor_laporan' },
            { data: 'pelapor', name: 'nama_pelapor' },
            { data: 'gedung', name: 'gedung.nama_gedung' },
            { data: 'jenis', name: 'jenisKerusakan.nama_jenis' },
            { data: 'petugas', name: 'petugasHarbang.user.name' },
            { data: 'status', name: 'status.nama' },
            { data: 'sla', name: 'prioritas.nama', orderable: false },
            { data: 'tanggal', name: 'created_at' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-end' },
        ],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
    });

    const filterIds = ['#filterStatus', '#filterPrioritas', '#filterGedung', '#filterJenis', '#filterPetugas', '#filterTanggalDari', '#filterTanggalSampai'];

    $(filterIds.join(', ')).on('change', function () {
        table.ajax.reload();
    });

    $('#btnResetFilter').on('click', function () {
        $(filterIds.join(', ')).val('');
        table.ajax.reload();
    });

    // Export membawa filter yang sedang aktif di layar, supaya file yang
    // diunduh selalu sama dengan yang sedang dilihat.
    function buildExportUrl(baseUrl) {
        const params = new URLSearchParams();
        params.set('status_id', $('#filterStatus').val() || '');
        params.set('prioritas_id', $('#filterPrioritas').val() || '');
        params.set('gedung_id', $('#filterGedung').val() || '');
        params.set('jenis_kerusakan_id', $('#filterJenis').val() || '');
        params.set('petugas_harbang_id', $('#filterPetugas').val() || '');
        params.set('tanggal_dari', $('#filterTanggalDari').val() || '');
        params.set('tanggal_sampai', $('#filterTanggalSampai').val() || '');
        return baseUrl + '?' + params.toString();
    }

    $('#btnExportExcel').on('click', function (e) {
        e.preventDefault();
        window.location.href = buildExportUrl('{{ route('laporan.export-excel') }}');
    });

    $('#btnExportPdf').on('click', function (e) {
        e.preventDefault();
        window.open(buildExportUrl('{{ route('laporan.export-pdf') }}'), '_blank');
    });
});
</script>
@endpush
