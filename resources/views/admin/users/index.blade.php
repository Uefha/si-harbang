@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
    <x-breadcrumb :items="['Kelola User']" />

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-semibold mb-0">Kelola User</h4>
            <p class="small text-secondary mb-0">Daftar seluruh akun lintas role. Untuk akun Pelapor/Petugas Harbang baru, gunakan menu Kelola Pelapor / Kelola Petugas Harbang.</p>
        </div>
        <button class="btn btn-primary btn-sm text-nowrap" onclick="modeTambahUser()">
            <i class="bi bi-plus-lg me-1"></i>Tambah Super Admin
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table id="tabelUser" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th width="90">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalUserCreate" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.users.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Akun Super Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">No. HP</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Kata Sandi <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalUserEdit" tabindex="-1">
        <div class="modal-dialog">
            <form id="formUserEdit" method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <input type="hidden" name="_edit_user_id" id="edit_user_id_field" value="{{ old('_edit_user_id') }}">
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="user_name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="user_email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">No. HP</label>
                        <input type="text" name="phone" id="user_phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <hr class="my-3">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Kata Sandi Baru <span class="text-secondary fw-normal">(opsional)</span></label>
                        <input type="password" name="password" id="user_password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                        <div class="form-text">Kosongkan kalau tidak ingin mengubah kata sandi. Dipakai untuk mengatur ulang kata sandi siapa pun yang lupa (termasuk akun Anda sendiri).</div>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" id="user_password_confirmation" class="form-control" autocomplete="new-password">
                    </div>
                    <hr class="my-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="user_aktif" value="1" @checked(old('is_active', true))>
                        <label class="form-check-label small">Akun Aktif</label>
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
    const updateUrlTemplate = "{{ route('admin.users.update', ['user' => '__ID__']) }}";

    const table = $('#tabelUser').DataTable({
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: "{{ route('admin.users.index') }}",
        columns: [
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'role_badge', name: 'role.display_name', orderable: false },
            { data: 'status', name: 'is_active', orderable: false },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
        ],
    });

    window.modeTambahUser = function () {
        new bootstrap.Modal(document.getElementById('modalUserCreate')).show();
    };

    window.editUser = function (id) {
        const rowData = table.rows().data().toArray().find(r => r.id === id);
        if (!rowData) return;
        document.getElementById('formUserEdit').action = updateUrlTemplate.replace('__ID__', id);
        document.getElementById('edit_user_id_field').value = id;
        document.getElementById('user_name').value = rowData.name ?? '';
        document.getElementById('user_email').value = rowData.email ?? '';
        document.getElementById('user_phone').value = rowData.phone ?? '';
        document.getElementById('user_aktif').checked = !!rowData.is_active;
        document.getElementById('user_password').value = '';
        document.getElementById('user_password_confirmation').value = '';
        new bootstrap.Modal(document.getElementById('modalUserEdit')).show();
    };

    // Kalau halaman ini dimuat ulang karena validasi modal Ubah Akun gagal,
    // skrip global di layout akan otomatis membuka lagi modalnya (karena ada
    // field bertanda "is-invalid") - tapi action form-nya perlu disusun ulang
    // di sini dulu, karena biasanya hanya diisi lewat editUser() saat diklik.
    const editUserId = document.getElementById('edit_user_id_field').value;
    if (editUserId) {
        document.getElementById('formUserEdit').action = updateUrlTemplate.replace('__ID__', editUserId);
    }
});
</script>
@endpush
