<script>
    function initSweetAlertDelete() {
        const formularios = document.querySelectorAll('.form-eliminar-registro');
        formularios.forEach(formulario => {
            formulario.removeEventListener('submit', handleFormSubmitDelete);
            formulario.addEventListener('submit', handleFormSubmitDelete);
        });
    }

    function handleFormSubmitDelete(e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡El registro será eliminado permanentemente del sistema!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f43f5e',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: { confirmButton: 'btn btn-danger px-4 rounded-pill', cancelButton: 'btn btn-secondary px-4 rounded-pill me-2' },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) { this.submit(); }
        });
    }

    // Asegurarnos de que funcione con la navegación reactiva de Livewire
    document.addEventListener('livewire:navigated', initSweetAlertDelete);
    document.addEventListener('livewire:load', initSweetAlertDelete);
    document.addEventListener('livewire:update', initSweetAlertDelete);
    
    // Inicializar al cargar la página
    initSweetAlertDelete();
</script>