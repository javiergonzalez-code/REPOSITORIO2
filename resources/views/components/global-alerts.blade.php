{{-- Directiva para las alertas que vienen desde el Controlador --}}
@include('sweetalert::alert')

{{-- Capturador global de errores de validación nativos de Laravel --}}
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Verifica tus datos',
                html: '<ul class="text-start mb-0">' + 
                      @foreach($errors->all() as $error)
                          '<li>{{ $error }}</li>' + 
                      @endforeach
                      '</ul>',
                confirmButtonColor: '#0f172a'
            });
        });
    </script>
@endif