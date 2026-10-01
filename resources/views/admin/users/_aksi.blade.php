<div class="d-flex gap-1">
    <button type="button" class="btn btn-sm btn-outline-primary" onclick="editUser({{ $row->id }})" title="Ubah">
        <i class="bi bi-pencil-square"></i>
    </button>
    @if ($canDelete)
        <form method="POST" action="{{ route('admin.users.destroy', $row) }}" class="d-inline form-hapus">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                <i class="bi bi-trash3"></i>
            </button>
        </form>
    @endif
</div>
