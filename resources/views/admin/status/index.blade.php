@extends('layouts.app')

@section('title', 'Status Laporan')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold mb-0">Status Laporan</h4>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i>Tambah Status
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table id="table-status" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr><th>Urutan</th><th>Status</th><th>Sifat</th><th class="text-end">Aksi</th></tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.status.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Nama Status <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Warna Badge <span class="text-danger">*</span></label>
                            <select name="warna_badge" class="form-select @error('warna_badge') is-invalid @enderror" required>
                                @foreach (['primary','secondary','success','danger','warning','info','dark'] as $c)
                                    <option value="{{ $c }}" {{ old('warna_badge') === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Urutan Tampil <span class="text-danger">*</span></label>
                            <input type="number" name="urutan" class="form-control @error('urutan') is-invalid @enderror" min="0" value="{{ old('urutan', 0) }}" required>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_final" value="1" {{ old('is_final') ? 'checked' : '' }} id="add_is_final">
                            <label class="form-check-label small" for="add_is_final">Status akhir (mis. Selesai / Ditolak — menghentikan hitungan SLA)</label>
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
                <form method="POST" id="formEditStatus" action="{{ old('_method') === 'PUT' ? url('admin/status/'.old('_id')) : '' }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_id" value="{{ old('_id') }}">
                    <div class="modal-header">
                        <h5 class="modal-title">Ubah Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Nama Status <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="edit_nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Warna Badge <span class="text-danger">*</span></label>
                            <select name="warna_badge" id="edit_warna_badge" class="form-select @error('warna_badge') is-invalid @enderror" required>
                                @foreach (['primary','secondary','success','danger','warning','info','dark'] as $c)
                                    <option value="{{ $c }}" {{ old('warna_badge') === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Urutan Tampil <span class="text-danger">*</span></label>
                            <input type="number" name="urutan" id="edit_urutan" class="form-control @error('urutan') is-invalid @enderror" value="{{ old('urutan') }}" min="0" required>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_final" value="1" id="edit_is_final" {{ old('is_final') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="edit_is_final">Status akhir</label>
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
    $('#table-status').DataTable({
        processing: true,
        responsive: true,
        serverSide: true,
        ordering: false,
        ajax: '{{ route('admin.status.index') }}',
        columns: [
            { data: 'urutan', name: 'urutan' },
            { data: 'badge', name: 'nama' },
            { data: 'final', name: 'is_final', orderable: false },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-end' },
        ],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
    });
});

function editStatus(data) {
    document.getElementById('formEditStatus').action = '{{ url('admin/status') }}/' + data.id;
    document.querySelector('#formEditStatus input[name="_id"]').value = data.id;
    document.getElementById('edit_nama').value = data.nama;
    document.getElementById('edit_warna_badge').value = data.warna_badge;
    document.getElementById('edit_urutan').value = data.urutan;
    document.getElementById('edit_is_final').checked = Number(data.is_final) === 1;
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>
@endpush
