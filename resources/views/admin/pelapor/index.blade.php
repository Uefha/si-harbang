@extends('layouts.app')

@section('title', 'Kelola Pelapor')

@section('content')
    <x-breadcrumb :items="['Kelola Pelapor']" />

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold mb-0">Kelola Pelapor</h4>
        <button class="btn btn-primary btn-sm" onclick="modeTambahPelapor()">
            <i class="bi bi-plus-lg me-1"></i>Tambah Pelapor
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table id="tabelPelapor" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jabatan</th>
                        <th>No. HP</th>
                        <th>Status</th>
                        <th width="90">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalPelapor" tabindex="-1">
        <div class="modal-dialog">
            <form id="formPelapor" method="POST" class="modal-content"
                  action="{{ old('_method') === 'PUT' ? url('admin/pelapor/'.old('_edit_id')) : route('admin.pelapor.store') }}">
                @csrf
                <input type="hidden" name="_method" id="pelaporMethod" value="{{ old('_method', 'POST') }}">
                <input type="hidden" name="_edit_id" value="{{ old('_edit_id') }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPelaporTitle">{{ old('_method') === 'PUT' ? 'Ubah Pelapor' : 'Tambah Pelapor' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="pelapor_name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="pelapor_email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium" id="pelapor_password_label">{{ old('_method') === 'PUT' ? 'Kata Sandi Baru (opsional)' : 'Kata Sandi' }} @if (old('_method') !== 'PUT')<span class="text-danger">*</span>@endif</label>
                            <input type="password" name="password" id="pelapor_password" class="form-control @error('password') is-invalid @enderror" {{ old('_method') !== 'PUT' ? 'required' : '' }}>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Konfirmasi Kata Sandi</label>
                            <input type="password" name="password_confirmation" id="pelapor_password_confirmation" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="jabatan" id="pelapor_jabatan" class="form-control @error('jabatan') is-invalid @enderror" value="{{ old('jabatan') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">No. HP <span class="text-danger">*</span></label>
                            <input type="text" name="no_hp" id="pelapor_no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">NIP/NIK</label>
                            <input type="text" name="nip_nik" id="pelapor_nip_nik" class="form-control @error('nip_nik') is-invalid @enderror" value="{{ old('nip_nik') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Unit Kerja</label>
                            <input type="text" name="unit_kerja" id="pelapor_unit_kerja" class="form-control @error('unit_kerja') is-invalid @enderror" value="{{ old('unit_kerja') }}">
                        </div>
                        <div class="col-12 form-check form-switch" id="pelapor_aktif_wrap" style="{{ old('_method') === 'PUT' ? '' : 'display:none' }}">
                            <input class="form-check-input" type="checkbox" name="is_active" id="pelapor_aktif" value="1" {{ old('is_active') ? 'checked' : '' }}>
                            <label class="form-check-label small">Akun Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
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
    const storeUrl = "{{ route('admin.pelapor.store') }}";
    const updateUrlTemplate = "{{ route('admin.pelapor.update', ['pelapor' => '__ID__']) }}";

    const table = $('#tabelPelapor').DataTable({
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: "{{ route('admin.pelapor.index') }}",
        columns: [
            { data: 'name', name: 'user.name' },
            { data: 'email', name: 'user.email' },
            { data: 'jabatan', name: 'jabatan' },
            { data: 'no_hp', name: 'no_hp' },
            { data: 'status', name: 'user.is_active', orderable: false },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
        ],
    });

    window.modeTambahPelapor = function () {
        document.getElementById('formPelapor').reset();
        document.getElementById('formPelapor').action = storeUrl;
        document.getElementById('pelaporMethod').value = 'POST';
        document.getElementById('modalPelaporTitle').innerText = 'Tambah Pelapor';
        document.getElementById('pelapor_password').required = true;
        document.getElementById('pelapor_password_confirmation').required = true;
        document.getElementById('pelapor_password_label').innerHTML = 'Kata Sandi <span class="text-danger">*</span>';
        document.getElementById('pelapor_aktif_wrap').style.display = 'none';
        new bootstrap.Modal(document.getElementById('modalPelapor')).show();
    };

    window.editPelapor = function (id) {
        const rowData = table.rows().data().toArray().find(r => r.id === id);
        if (!rowData) return;
        document.getElementById('formPelapor').action = updateUrlTemplate.replace('__ID__', id);
        document.getElementById('pelaporMethod').value = 'PUT';
        document.getElementById('modalPelaporTitle').innerText = 'Ubah Pelapor';
        document.getElementById('pelapor_password').required = false;
        document.getElementById('pelapor_password_confirmation').required = false;
        document.getElementById('pelapor_password_label').innerText = 'Kata Sandi Baru (opsional)';
        document.getElementById('pelapor_password').value = '';
        document.getElementById('pelapor_password_confirmation').value = '';
        document.getElementById('pelapor_name').value = rowData.name ?? '';
        document.getElementById('pelapor_email').value = rowData.email ?? '';
        document.getElementById('pelapor_jabatan').value = rowData.jabatan ?? '';
        document.getElementById('pelapor_no_hp').value = rowData.no_hp ?? '';
        document.getElementById('pelapor_nip_nik').value = rowData.nip_nik ?? '';
        document.getElementById('pelapor_unit_kerja').value = rowData.unit_kerja ?? '';
        document.getElementById('pelapor_aktif_wrap').style.display = 'block';
        document.getElementById('pelapor_aktif').checked = !!(rowData.user && rowData.user.is_active);
        new bootstrap.Modal(document.getElementById('modalPelapor')).show();
    };
});
</script>
@endpush
