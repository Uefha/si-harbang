<div class="d-flex gap-1 justify-content-end">
    <button type="button" class="btn btn-sm btn-outline-primary" onclick='editFasilitas(@json($row))' title="Ubah">
        <i class="bi bi-pencil-square"></i>
    </button>
    <form method="POST" action="{{ route('admin.fasilitas.destroy', $row) }}" class="d-inline form-hapus" data-confirm-message="Hapus fasilitas &quot;{{ $row->nama_fasilitas }}&quot;? Data yang sudah dihapus tidak dapat dikembalikan.">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
    </form>
</div>
