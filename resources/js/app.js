// Importa la configuración base de bootstrap (axios, lodash, etc.) necesaria para Laravel
import './bootstrap';

// Espera a que todo el contenido del DOM (HTML) esté cargado antes de ejecutar el script
document.addEventListener('DOMContentLoaded', () => {
    
    // Selecciona el botón de alternancia mediante su ID 'dark-mode-toggle'
    const toggleBtn = document.getElementById('dark-mode-toggle');
    
    // Selecciona el elemento del icono (la luna o el sol) dentro del botón
    const icon = document.getElementById('dark-mode-icon');
    
    // Referencia al elemento <body> para aplicar o quitar la clase del modo oscuro
    const body = document.body;
    const h3= document.h3;

    // --- PERSISTENCIA: Verificar si ya existía una preferencia guardada en el navegador ---
    // localStorage permite guardar datos que no se borran al recargar la página
    if (localStorage.getItem('theme') === 'dark') {
        
        // Si la preferencia era 'dark', añade la clase CSS 'dark-mode' al body de inmediato
        body.classList.add('dark-mode');
        
        // Cambia visualmente el icono de la luna (fa-moon) por el del sol (fa-sun)
        icon.classList.replace('fa-moon', 'fa-sun');
    }

    // --- INTERACCIÓN: Escuchar el clic en el botón de cambio de tema ---
    toggleBtn.addEventListener('click', () => {
        
        // 'toggle' añade la clase 'dark-mode' si no existe, y la quita si ya existe
        body.classList.toggle('dark-mode');
        
        // Verifica si después del cambio la clase 'dark-mode' quedó activa
        if (body.classList.contains('dark-mode')) {
            
            // Si el modo oscuro está activo, guarda la palabra 'dark' en el almacenamiento local
            localStorage.setItem('theme', 'dark');
            
            // Cambia el icono a sol para indicar que el próximo clic volverá al modo claro
            icon.classList.replace('fa-moon', 'fa-sun');
            
        } else {
            
            // Si el modo oscuro se desactivó, guarda 'light' en el almacenamiento local
            localStorage.setItem('theme', 'light');
            
            // Cambia el icono de vuelta a la luna para indicar que se puede volver a oscurecer
            icon.classList.replace('fa-sun', 'fa-moon');
        }
    });
});