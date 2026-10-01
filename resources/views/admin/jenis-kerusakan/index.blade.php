@extends('layouts.app')

@section('title', 'Jenis Kerusakan')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold mb-0">Jenis Kerusakan</h4>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i>Tambah Jenis
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table id="table-jenis" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr><th>Icon</th><th>Nama Jenis</th><th>Deskripsi</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.jenis-kerusakan.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Jenis Kerusakan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Nama Jenis <span class="text-danger">*</span></label>
                            <input type="text" name="nama_jenis" class="form-control @error('nama_jenis') is-invalid @enderror" value="{{ old('nama_jenis') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Icon (Bootstrap Icons)</label>
                            <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon') }}" placeholder="mis. bi-lightning-charge">
                            <div class="form-text">Lihat daftar nama icon di <a href="https://icons.getbootstrap.com" target="_blank" rel="noopener">icons.getbootstrap.com</a>.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="2">{{ old('deskripsi') }}</textarea>
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
                <form method="POST" id="formEditJenis" action="{{ old('_method') === 'PUT' ? url('admin/jenis-kerusakan/'.old('_id')) : '' }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_id" value="{{ old('_id') }}">
                    <div class="modal-header">
                        <h5 class="modal-title">Ubah Jenis Kerusakan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Nama Jenis <span class="text-danger">*</span></label>
                            <input type="text" name="nama_jenis" id="edit_nama_jenis" class="form-control @error('nama_jenis') is-invalid @enderror" value="{{ old('nama_jenis') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Icon (Bootstrap Icons)</label>
                            <input type="text" name="icon" id="edit_icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Deskripsi</label>
                            <textarea name="deskripsi" id="edit_deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="2">{{ old('deskripsi') }}</textarea>
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
    $('#table-jenis').DataTable({
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: '{{ route('admin.jenis-kerusakan.index') }}',
        columns: [
            { data: 'icon', name: 'icon', orderable: false, render: (d) => d ? `<i class="bi ${d}"></i>` : '—' },
            { data: 'nama_jenis', name: 'nama_jenis' },
            { data: 'deskripsi', name: 'deskripsi', defaultContent: '—' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-end' },
        ],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
    });
});

function editJenis(data) {
    document.getElementById('formEditJenis').action = '{{ url('admin/jenis-kerusakan') }}/' + data.id;
    document.querySelector('#formEditJenis input[name="_id"]').value = data.id;
    document.getElementById('edit_nama_jenis').value = data.nama_jenis;
    document.getElementById('edit_icon').value = data.icon ?? '';
    document.getElementById('edit_deskripsi').value = data.deskripsi ?? '';
    document.getElementById('edit_is_active').checked = Number(data.is_active) === 1;
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>
@endpush
