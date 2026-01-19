@extends('layouts.app')
{{-- Vista sugerida para input.index --}}
<div class="card shadow-sm border-0 mt-4">
    <div class="card-body p-5 text-center">
        <i class="fas fa-cloud-upload-alt fa-4x text-primary mb-3"></i>
        <h4>Subir Documentación</h4>
        <p class="text-muted">Arrastra los archivos aquí o haz clic para buscar</p>
        
        <form action="{{ route('input.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="archivo" class="form-control mb-3" required>
            <button type="submit" class="btn btn-ragon-modern w-100">Cargar al Repositorio</button>
        </form>
    </div>
</div>