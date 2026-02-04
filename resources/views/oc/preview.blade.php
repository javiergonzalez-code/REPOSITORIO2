@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">

                {{-- RECUADRO 1: ENCABEZADO DE PREVISUALIZACIÓN --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <div class="header-icon-box shadow-sm me-3"
                                style="background: #0f172a; color: #3b82f6; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                                <i
                                    class="fas {{ in_array($extension, ['xlsx', 'xls', 'csv']) ? 'fa-file-excel' : 'fa-file-code' }} fa-lg"></i>
                            </div>
                            <div>
                                <h2 class="audit-title mb-0"
                                    style="font-size: 1.4rem; font-weight: 800; color: #1e293b; letter-spacing: -0.5px;">
                                    {{ $oc->nombre_original }}
                                </h2>
                                <div class="audit-subtitle d-flex align-items-center"
                                    style="font-size: 1rem; color: #64748b; font-weight: 700;">
                                    <span
                                        class="badge bg-primary-light text-primary border-primary-subtle px-2 py-1 rounded-pill me-2 text-uppercase"
                                        style="font-size: 1rem;">
                                        Formato: {{ $extension }}
                                    </span>
                                    <span class="divider-v mx-2"
                                        style="height: 12px; width: 1px; background: #cbd5e1;"></span>
                                    <i class="fas fa-user-circle me-1 font-size: 1rem"></i> Subido por: {{ $oc->user->name }}
                                </div>
                            </div>
                        </div>
                        <div class="header-actions mt-5">
                            <a href="{{ route('oc.index') }}" class="btn-ragon-outline shadow-sm">
                                <i class="fas fa-arrow-left me-2"></i> VOLVER AL LISTADO
                            </a>
                            <a href="{{ route('oc.download', $oc->id) }}"
                                class="btn btn-gradient rounded-pill">
                                <i class="fas fa-download me-2"></i> DESCARGAR
                            </a>
                        </div>
                    </div>
                </div>

                {{-- RECUADRO 2: CONTENIDO DEL ARCHIVO --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="card-header bg-light border-0 py-3 px-4 d-flex align-items-center">
                        <h6 class="text-uppercase fw-black mb-0 text-muted" style="font-size: 1rem; letter-spacing: 1px;">
                            <i class="fas fa-table me-2"></i> Lectura de datos del sistema
                        </h6>
                        <div class="ms-auto text-muted fw-bold font-size: 1rem">
                            Mostrando contenido procesado
                        </div>
                    </div>
                    <div class="card-body p-0">
                        {{-- Contenedor con scroll personalizado --}}
                        <div class="table-responsive" style="max-height: 650px; overflow-y: auto;">
                            <table class="table table-hover table-striped-columns align-middle mb-0">
                                <tbody class="font-monospace" style="font-size: 0.85rem;">
                                    @forelse($data as $index => $row)
                                        <tr class="log-row">
                                            {{-- Indicador de número de fila --}}
                                            <td class="bg-light text-center text-muted border-end p-2"
                                                style="width: 50px; font-size: 0.7rem; font-weight: 800;">
                                                {{ $index + 1 }}
                                            </td>

                                            @foreach ($row as $cell)
                                                <td class="p-3 border-end" style="min-width: 150px; color: #334155;">
                                                    {{ $cell }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center py-5">
                                                <div class="py-4">
                                                    <i class="fas fa-database fa-3x text-light mb-3"></i>
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

            </div>
        </div>
    </div>


@endsection
