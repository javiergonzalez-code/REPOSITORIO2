@props(['action'])

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" id="upload-form">
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

    {{-- Botón de subida --}}
    <button type="submit" id="btnSubir" class="btn-ragon-modern w-100 py-3 shadow" disabled>
        <i class="fas fa-upload me-2"></i> SUBIR AL REPOSITORIO
    </button>

    {{-- Contenedor de la barra de carga --}}
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

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.add('upload-zone-active');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.remove('upload-zone-active');
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                fileInput.files = files; 
                updateDisplay(files[0]);
            }
        }, false);

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                updateDisplay(this.files[0]);
            } else {
                fileInfo.classList.add('d-none');
                btnSubir.disabled = true;
            }
        });

        function updateDisplay(file) {
            let fileSize = (file.size / 1024 / 1024).toFixed(2);
            fileNameDisplay.innerText = `${file.name} (${fileSize} MB)`;
            fileInfo.classList.remove('d-none');
            btnSubir.disabled = false; 
        }

        formSubida.addEventListener('submit', function(e) {
            btnSubir.disabled = true;
            btnSubir.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Procesando Archivo...';
            progressContainer.classList.remove('d-none');

            let width = 0;
            let interval = setInterval(function() {
                if (width >= 90) {
                    clearInterval(interval); 
                } else {
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