@props(['viewRoute' => null, 'downloadRoute' => null, 'deleteRoute' => null])

<div class="d-flex justify-content-end gap-2">
    @if($viewRoute)
        <a href="{{ $viewRoute }}" class="action-btn btn-view" title="Ver">
            <i class="fas fa-eye"></i>
        </a>
    @endif

    @if($downloadRoute)
        <a href="{{ $downloadRoute }}" class="action-btn btn-download" title="Descargar">
            <i class="fas fa-cloud-download-alt"></i>
        </a>
    @endif

    @if($deleteRoute)
        <form action="{{ $deleteRoute }}" method="POST" class="d-inline form-eliminar-registro">
            @csrf
            @method('DELETE')
            <button type="submit" class="action-btn btn-delete" title="Eliminar">
                <i class="fas fa-trash-alt"></i>
            </button>
        </form>
    @endif
</div>