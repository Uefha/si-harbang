<div class="d-flex gap-1">
    <button type="button" class="btn btn-sm btn-outline-primary" {!! $editAttrs !!} title="Ubah">
        <i class="bi bi-pencil-square"></i>
    </button>
    <form method="POST" action="{{ $deleteRoute }}" class="d-inline form-hapus">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
            <i class="bi bi-trash3"></i>
        </button>
    </form>
</div>
