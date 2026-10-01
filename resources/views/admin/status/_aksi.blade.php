<div class="d-flex gap-1 justify-content-end">
    <button type="button" class="btn btn-sm btn-outline-primary" onclick='editStatus(@json($row))' title="Ubah">
        <i class="bi bi-pencil-square"></i>
    </button>
    <form method="POST" action="{{ route('admin.status.destroy', $row) }}" class="d-inline form-hapus" data-confirm-message="Hapus status &quot;{{ $row->nama }}&quot;? Pastikan tidak ada laporan yang memakainya.">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
    </form>
</div>
