@extends('layouts.app')

@section('content')
<div class="login-wrapper">
    <div class="card login-card card-large shadow-lg">
        
        {{-- Notificaciones --}}
        @if (session('success'))
            <div class="alert alert-custom alert-success-ragon animate__animated animate__fadeInDown">
                <i class="fas fa-check-circle me-3 fa-2x"></i>
                <div>
                    <strong>¡Éxito!</strong>
                    <p class="mb-0">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="card-header login-header-modern py-5">
            <div class="brand-icon-wrapper mb-3">
                <i class="fas fa-file-import"></i>
            </div>
            <h2 class="h3 fw-bold mb-0">CARGA DE DATOS</h2>
            <span class="badge bg-blue-ragon mt-2 px-3">SISTEMA DE GESTIÓN RAGON</span>
        </div>

        <div class="card-body p-5">
            <form action="{{ route('input.store') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                @csrf
                
                {{-- Área de Arrastre Mejorada --}}
                <div class="upload-zone mb-4" id="drop-zone">
                    <input type="file" name="archivo" id="archivo" class="file-input-hidden" required>
                    <label for="archivo" class="upload-label">
                        <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-primary icon-bounce"></i>
                        <span class="d-block fw-bold text-dark h5">Arrastra tu archivo aquí</span>
                        <span class="text-muted small">o haz clic para buscar en tu equipo</span>
                        <div id="file-info" class="mt-3 d-none">
                            <span class="badge bg-primary p-2">
                                <i class="fas fa-file me-2"></i><span id="file-name-display"></span>
                            </span>
                        </div>
                    </label>
                </div>

                <button type="submit" class="btn-ragon-modern w-100 py-3 shadow">
                    <i class="fas fa-upload me-2"></i> SUBIR AL REPOSITORIO
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('archivo');
    const fileNameDisplay = document.getElementById('file-name-display');
    const fileInfo = document.getElementById('file-info');

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
        fileInput.files = files; // Asignar archivos al input oculto
        updateDisplay(files[0]);
    }, false);

    // Manejar selección normal por clic
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            updateDisplay(this.files[0]);
        }
    });

    function updateDisplay(file) {
        fileNameDisplay.innerText = file.name;
        fileInfo.classList.remove('d-none');
    }
</script>
@endsection