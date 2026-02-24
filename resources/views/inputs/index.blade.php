@extends('layouts.app')

@section('content')
    <div class="login-wrapper">
        <div class="card login-card card-large shadow-lg">

            {{-- Área de Notificaciones --}}
            @if (session('success'))
                <div class="alert alert-custom alert-success-ragon animate__animated animate__fadeInDown">
                    <i class="fas fa-check-circle me-3 fa-2x"></i>
                    <div>
                        <strong>¡Éxito!</strong>
                        <p class="mb-0">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- Notificación Roja (Error) --}}
            @if (session('error') || $errors->any())
                <div class="alert alert-custom alert-error-ragon animate__animated animate__fadeInDown">
                    <i class="fas fa-exclamation-triangle me-3 fa-2x"></i>
                    <div>
                        <strong>Archivo No Válido</strong>
                        <p class="mb-0">
                            @if (session('error'))
                                {{ session('error') }}
                            @else
                                {{ $errors->first() }} {{-- Muestra el primer error de validación --}}
                            @endif
                        </p>
                    </div>
                </div>
            @endif


            <div class="card-header login-header-modern ">
                <div class="brand-icon-wrapper ">
                    <i class="fas fa-file-import"></i>
                </div>
                <h2 class="h3 fw-bold mb-0 mt-2">CARGA DE DATOS</h2>
                <span class="badge bg-blue-ragon mt-2 px-3">SISTEMA DE GESTIÓN RAGON</span>

                <div class="header-actions mt-4">
                    <a href="{{ route('home') }}" class="btn-ragon-outline">
                        <i class="fas fa-th-large me-2"></i> PANEL DE CONTROL
                    </a>
                </div>
            </div>


            <div class="w-162 mt-4 text-center ">
                <div class="format-notice-pill">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="pulse-icon">
                            <i class="fas fa-info-circle"></i>
                        </span>
                        <p class="mb-0 mx-2">
                            Formatos permitidos:
                            <span class="badge-format">CSV</span>
                            <span class="badge-format">XML</span>
                            <span class="badge-format">XLSX</span>
                        </p>

                        <span class="size-limit">MÁX. 5MB</span>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                {{-- Se ajustó el ID a upload-form para conectarlo con el JS --}}
                <form action="{{ route('input.store') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                    @csrf

                    {{-- Área de Arrastre --}}
                    <div class="upload-zone mb-4" id="drop-zone">
                        <input type="file" name="archivo" id="archivo" class="file-input-hidden"
                            accept=".csv,.xlsx,.xls,.xml" required>

                        <label for="archivo" class="upload-label">
                            <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-primary icon-bounce"></i>
                            <span class="d-block fw-bold text-dark h5">Arrastra tu archivo aquí</span>
                            <span class="text-muted small">o haz clic para buscar</span>

                            <div id="file-info" class="mt-3 d-none">
                                <span class="badge bg-success p-2 text-white">
                                    <i class="fas fa-check-circle me-1"></i> <span id="file-name-display"></span>
                                </span>
                            </div>
                        </label>
                    </div>

                    {{-- Mostrar Errores de Validación fuera de la zona de clic --}}
                    @if ($errors->any())
                        <div class="alert alert-danger shadow-sm border-0 rounded-3 animate__animated animate__shakeX">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-ban me-3 fa-2x"></i>
                                <ul class="mb-0 list-unstyled">
                                    @foreach ($errors->all() as $error)
                                        <li><strong>Error:</strong> {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    {{-- Botón de subida (Inicia deshabilitado hasta que se ponga un archivo) --}}
                    <button type="submit" id="btnSubir" class="btn-ragon-modern w-100 py-3 shadow" disabled>
                        <i class="fas fa-upload me-2"></i> SUBIR AL REPOSITORIO
                    </button>

                    {{-- Contenedor de la barra de carga (Oculto por defecto) --}}
                    <div id="progressContainer" class="mt-4 d-none text-start">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small fw-bold" id="progressText">
                                <i class="fas fa-cog fa-spin me-1"></i> Subiendo y procesando datos...
                            </span>
                            <span class="text-primary small fw-bold" id="progressPercent">0%</span>
                        </div>
                        <div class="progress" style="height: 12px; border-radius: 10px;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('archivo');
            const fileNameDisplay = document.getElementById('file-name-display');
            const fileInfo = document.getElementById('file-info');
            const btnSubir = document.getElementById('btnSubir');
            const formSubida = document.getElementById('upload-form');
            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            const progressPercent = document.getElementById('progressPercent');

            // --- 1. LÓGICA DE DRAG & DROP ---
            
            // Resaltar la zona al arrastrar
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.classList.add('upload-zone-active');
                }, false);
            });

            // Quitar resaltado al salir
            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('upload-zone-active');
                }, false);
            });

            // Manejar el archivo soltado
            dropZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length > 0) {
                    fileInput.files = files; // Asignar al input oculto
                    updateDisplay(files[0]);
                }
            }, false);

            // Manejar selección normal por clic en el input
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    updateDisplay(this.files[0]);
                } else {
                    // Si el usuario cancela la selección
                    fileInfo.classList.add('d-none');
                    btnSubir.disabled = true;
                }
            });

            // Función para actualizar la vista cuando hay un archivo
            function updateDisplay(file) {
                // Calcular peso en MB
                let fileSize = (file.size / 1024 / 1024).toFixed(2);
                fileNameDisplay.innerText = `${file.name} (${fileSize} MB)`;
                fileInfo.classList.remove('d-none');
                btnSubir.disabled = false; // Habilitar el botón de subida
            }

            // --- 2. LÓGICA DE ANIMACIÓN DE SUBIDA ---

            formSubida.addEventListener('submit', function(e) {
                // A. Bloquear botón y mostrar spinner de FontAwesome
                btnSubir.disabled = true;
                btnSubir.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Procesando Archivo...';

                // B. Mostrar barra de progreso
                progressContainer.classList.remove('d-none');

                // C. Simulación del avance de la barra
                let width = 0;
                let interval = setInterval(function() {
                    // La barra se detendrá en 90% esperando a que Laravel termine y recargue la página
                    if (width >= 90) {
                        clearInterval(interval); 
                    } else {
                        // Sube rápido al inicio, más lento al final
                        let increment = width < 50 ? 5 : (width < 80 ? 2 : 1);
                        width += increment;
                        
                        progressBar.style.width = width + '%';
                        progressPercent.textContent = width + '%';
                        progressBar.setAttribute('aria-valuenow', width);
                    }
                }, 200); 
            });
        });
    </script>
@endsection

