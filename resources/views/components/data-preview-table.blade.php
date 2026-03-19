@props(['data'])

<div class="card border-0 shadow-sm rounded-4 custom-card overflow-hidden">
    <div class="card-header bg-transparent border-0 py-3 px-4 d-flex align-items-center border-bottom">
        <h6 class="text-uppercase fw-black mb-0 text-muted" style="font-size: 0.9rem; letter-spacing: 1px;">
            <i class="fas fa-table me-2"></i> Lectura de datos del sistema
        </h6>
        <div class="ms-auto text-muted fw-bold small">
            Mostrando contenido procesado
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 650px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0">
                <tbody class="font-monospace" style="font-size: 0.85rem;">
                    @forelse($data as $index => $row)
                        <tr class="log-row">
                            <td class="text-center text-muted border-end p-2"
                                style="width: 50px; font-size: 0.7rem; font-weight: 800; background: rgba(0,0,0,0.02);">
                                {{ $loop->iteration }} </td>

                            @if (is_iterable($row))
                                {{-- If $row is an array/object, loop through its cells --}}
                                @foreach ($row as $cell)
                                    <td class="p-3 border-end text-main" style="min-width: 150px">
                                        {{ is_array($cell) ? json_encode($cell) : $cell }}
                                    </td>
                                @endforeach
                            @else
                                {{-- If $row is just a string or number, print it directly in one cell --}}
                                <td class="p-3 border-end text-main" style="min-width: 150px">
                                    {{ $row }}
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-5">
                                <div class="py-4">
                                    <i class="fas fa-database fa-3x text-muted mb-3 opacity-25"></i>
                                    <p class="text-muted fw-bold">El archivo no contiene datos legibles.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
