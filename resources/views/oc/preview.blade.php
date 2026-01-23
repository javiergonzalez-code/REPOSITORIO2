@extends('layouts.app')

@section('content')
    <style>
        :root {
            --primary-blue: #1a237e;
            --accent-blue: #3949ab;
            --soft-bg: #f0f2f9;
            --xml-bg: #282c34;
            /* Color estilo One Dark */
        }

        body {
            background-color: var(--soft-bg);
            font-family: 'Inter', sans-serif;
        }

        /* Card Principal con efecto de elevación */
        .preview-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        /* Panel de estadísticas rápidas */
        .stat-pill {
            background: white;
            padding: 10px 20px;
            border-radius: 15px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Estilo de Tabla Pro */
        .table-container {
            max-height: 550px;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        .table-container::-webkit-scrollbar {
            width: 6px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }

        .table thead th {
            position: sticky;
            top: 0;
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 15px 24px;
            border: none;
            z-index: 20;
        }

        .table tbody tr {
            transition: all 0.2s;
        }

        .table tbody tr:hover {
            background-color: #f1f5f9;
            transform: scale(1.002);
        }

        /* Visor XML Estilo VS Code */
        .xml-window {
            background: var(--xml-bg);
            border-radius: 16px;
            padding: 25px;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .xml-line {
            font-family: 'Fira Code', monospace;
            font-size: 0.85rem;
            line-height: 1.8;
            border-left: 2px solid #3e4451;
            padding-left: 15px;
        }

        .xml-tag {
            color: #e06c75;
        }

        /* Rojo suave */
        .xml-attr {
            color: #d19a66;
        }

        /* Naranja */
        .xml-value {
            color: #98c379;
        }

        /* Verde */
        .xml-text {
            color: #abb2bf;
        }

        /* Gris claro */

        .btn-action {
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }
    </style>

    <div class="container py-5">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>
                <a href="{{ route('oc.index') }}" class="text-decoration-none text-muted fw-medium small">
                    <i class="fas fa-chevron-left me-1"></i> Volver al Repositorio
                </a>
                <h2 class="fw-bold text-dark mt-2 mb-0"></h2>
            </div>
            <div class="d-flex gap-2">
                <div class="stat-pill shadow-sm">
                    <i class="fas fa-file-alt text-primary"></i>
                    <span class="small fw-bold">{{ strtoupper($extension) }}: {{ $oc->nombre_original }}</span>
                </div>
                <a href="{{ route('oc.download', $oc->id) }}" class="btn btn-primary btn-action shadow-sm mt-5">
                    <i class="fas fa-cloud-download-alt me-2 mt-5"></i>Descargar 
                </a>
            </div>
        </div>

        <div class="card preview-card">
            <div class="card-body p-0">

                <div class="p-4 bg-white border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-soft-primary p-3" style="background: #eef2ff">
                            <i class="fas fa-user-circle fs-4 text-primary"></i>
                            <p class="mb-0 small text-muted">Subido por:</p>
                            <p class="mb-0 fw-bold">{{ $oc->user->name }}</p>
                        </div>
                        <div>

                        </div>
                    </div>
                    <div class="text-end d-none d-md-block">
                        <p class="mb-0 small text-muted">Fecha de carga:</p>
                        <p class="mb-0 fw-bold">{{ $oc->created_at->format('d M, Y - H:i') }}</p>
                    </div>
                </div>

                @if ($extension == 'csv')
                    <div class="table-container">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    @foreach ($data[0] as $header)
                                        <th>{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (array_slice($data, 1) as $row)
                                    <tr>
                                        @foreach ($row as $cell)
                                            <td class="px-4 py-3 text-secondary">{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4">
                        <div class="xml-window">
                            <div class="d-flex gap-2 mb-3">
                                <div style="width:12px; height:12px; background:#ff5f56; border-radius:50%"></div>
                                <div style="width:12px; height:12px; background:#ffbd2e; border-radius:50%"></div>
                                <div style="width:12px; height:12px; background:#27c93f; border-radius:50%"></div>
                            </div>
                            <div class="table-responsive">
                                @foreach ($data as $key => $value)
                                    <div class="xml-line">
                                        <span class="xml-tag">&lt;{{ $key }}&gt;</span>
                                        @if (is_array($value))
                                            <div class="ps-4">
                                                @foreach ($value as $subKey => $subValue)
                                                    <div>
                                                        <span class="xml-tag">&lt;{{ $subKey }}&gt;</span>
                                                        <span
                                                            class="xml-text">{{ is_array($subValue) ? '{...}' : $subValue }}</span>
                                                        <span class="xml-tag">&lt;/{{ $subKey }}&gt;</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="xml-text">{{ $value }}</span>
                                        @endif
                                        <span class="xml-tag">&lt;/{{ $key }}&gt;</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>


    </div>
@endsection
