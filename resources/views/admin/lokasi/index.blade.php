@extends('layouts.app')

@section('title', 'Data Lokasi')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold mb-0">Data Lokasi</h4>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i>Tambah Lokasi
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table id="table-lokasi" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr><th>Nama Lokasi</th><th>Keterangan</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.lokasi.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Lokasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Nama Lokasi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lokasi" class="form-control @error('nama_lokasi') is-invalid @enderror" value="{{ old('nama_lokasi') }}" required>
                            @error('nama_lokasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Keterangan</label>
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="2">{{ old('keterangan') }}</textarea>
                            @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ (!$errors->any() || old('is_active')) ? 'checked' : '' }} id="add_is_active">
                            <label class="form-check-label small" for="add_is_active">Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="formEditLokasi" action="{{ old('_method') === 'PUT' ? url('admin/lokasi/'.old('_id')) : '' }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_id" value="{{ old('_id') }}">
                    <div class="modal-header">
                        <h5 class="modal-title">Ubah Lokasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Nama Lokasi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lokasi" id="edit_nama_lokasi" class="form-control @error('nama_lokasi') is-invalid @enderror" value="{{ old('nama_lokasi') }}" required>
                            @error('nama_lokasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Keterangan</label>
                            <textarea name="keterangan" id="edit_keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="2">{{ old('keterangan') }}</textarea>
                            @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active" {{ old('is_active') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="edit_is_active">Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
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
    $('#table-lokasi').DataTable({
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: '{{ route('admin.lokasi.index') }}',
        columns: [
            { data: 'nama_lokasi', name: 'nama_lokasi' },
            { data: 'keterangan', name: 'keterangan', defaultContent: '—' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-end' },
        ],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
    });
});

function editLokasi(data) {
    document.getElementById('formEditLokasi').action = '{{ url('admin/lokasi') }}/' + data.id;
    document.getElementById('edit_nama_lokasi').value = data.nama_lokasi;
    document.getElementById('edit_keterangan').value = data.keterangan ?? '';
    document.getElementById('edit_is_active').checked = Number(data.is_active) === 1;
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>
@endpush
