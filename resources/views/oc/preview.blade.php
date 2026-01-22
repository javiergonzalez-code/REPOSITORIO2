@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-0">Vista Previa: {{ $oc->nombre_original }}</h4>
                <p class="text-muted mb-0 small text-uppercase">Analizando contenido del archivo</p>
            </div>
            <a href="{{ route('oc.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    @if($extension == 'csv')
                        {{-- Renderizado para CSV --}}
                        <thead class="bg-light">
                            <tr>
                                @foreach($data[0] as $header)
                                    <th class="py-3 px-4 text-uppercase small fw-bold">{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_slice($data, 1) as $row)
                                <tr>
                                    @foreach($row as $cell)
                                        <td class="px-4 py-3">{{ $cell }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    @else
                        {{-- Renderizado simple para XML --}}
                        <div class="p-5 text-center">
                            <pre class="text-start bg-dark text-light p-4 rounded-3"><code>{{ print_r($data, true) }}</code></pre>
                        </div>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection