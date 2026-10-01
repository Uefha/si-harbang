@extends('layouts.app')

@section('title', 'Kelola Petugas Harbang')

@section('content')
    <x-breadcrumb :items="['Kelola Petugas Harbang']" />

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold mb-0">Kelola Petugas Harbang</h4>
        <button class="btn btn-primary btn-sm" onclick="modeTambahPetugas()">
            <i class="bi bi-plus-lg me-1"></i>Tambah Petugas
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table id="tabelPetugas" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Spesialisasi</th>
                        <th>No. HP</th>
                        <th>Status</th>
                        <th width="90">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalPetugas" tabindex="-1">
        <div class="modal-dialog">
            <form id="formPetugas" method="POST" class="modal-content">
                @csrf
                <input type="hidden" name="_method" id="petugasMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPetugasTitle">Tambah Petugas Harbang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="petugas_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="petugas_email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium" id="petugas_password_label">Kata Sandi <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="petugas_password" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Konfirmasi Kata Sandi</label>
                            <input type="password" name="password_confirmation" id="petugas_password_confirmation" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="jabatan" id="petugas_jabatan" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">No. HP <span class="text-danger">*</span></label>
                            <input type="text" name="no_hp" id="petugas_no_hp" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">NIP</label>
                            <input type="text" name="nip" id="petugas_nip" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Spesialisasi</label>
                            <input type="text" name="spesialisasi" id="petugas_spesialisasi" class="form-control" placeholder="mis. Listrik & Elektronik">
                        </div>
                        <div class="col-12 form-check form-switch" id="petugas_aktif_wrap" style="display:none">
                            <input class="form-check-input" type="checkbox" name="is_active" id="petugas_aktif" value="1">
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
    const storeUrl = "{{ route('admin.petugas-harbang.store') }}";
    const updateUrlTemplate = "{{ route('admin.petugas-harbang.update', ['petugasHarbang' => '__ID__']) }}";

    const table = $('#tabelPetugas').DataTable({
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: "{{ route('admin.petugas-harbang.index') }}",
        columns: [
            { data: 'name', name: 'user.name' },
            { data: 'email', name: 'user.email' },
            { data: 'spesialisasi', name: 'spesialisasi', defaultContent: '-' },
            { data: 'no_hp', name: 'no_hp' },
            { data: 'status', name: 'user.is_active', orderable: false },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
        ],
    });

    window.modeTambahPetugas = function () {
        document.getElementById('formPetugas').reset();
        document.getElementById('formPetugas').action = storeUrl;
        document.getElementById('petugasMethod').value = 'POST';
        document.getElementById('modalPetugasTitle').innerText = 'Tambah Petugas Harbang';
        document.getElementById('petugas_password').required = true;
        document.getElementById('petugas_password_confirmation').required = true;
        document.getElementById('petugas_password_label').innerHTML = 'Kata Sandi <span class="text-danger">*</span>';
        document.getElementById('petugas_aktif_wrap').style.display = 'none';
        new bootstrap.Modal(document.getElementById('modalPetugas')).show();
    };

    window.editPetugas = function (id) {
        const rowData = table.rows().data().toArray().find(r => r.id === id);
        if (!rowData) return;
        document.getElementById('formPetugas').action = updateUrlTemplate.replace('__ID__', id);
        document.getElementById('petugasMethod').value = 'PUT';
        document.getElementById('modalPetugasTitle').innerText = 'Ubah Petugas Harbang';
        document.getElementById('petugas_password').required = false;
        document.getElementById('petugas_password_confirmation').required = false;
        document.getElementById('petugas_password_label').innerText = 'Kata Sandi Baru (opsional)';
        document.getElementById('petugas_password').value = '';
        document.getElementById('petugas_password_confirmation').value = '';
        document.getElementById('petugas_name').value = rowData.name ?? '';
        document.getElementById('petugas_email').value = rowData.email ?? '';
        document.getElementById('petugas_jabatan').value = rowData.jabatan ?? '';
        document.getElementById('petugas_no_hp').value = rowData.no_hp ?? '';
        document.getElementById('petugas_nip').value = rowData.nip ?? '';
        document.getElementById('petugas_spesialisasi').value = rowData.spesialisasi ?? '';
        document.getElementById('petugas_aktif_wrap').style.display = 'block';
        document.getElementById('petugas_aktif').checked = !!(rowData.user && rowData.user.is_active);
        new bootstrap.Modal(document.getElementById('modalPetugas')).show();
    };
});
</script>
@endpush
