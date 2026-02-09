import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('dark-mode-toggle');
    const icon = document.getElementById('dark-mode-icon');
    const body = document.body;

    // Persistencia
    if (localStorage.getItem('theme') === 'dark') {
        body.classList.add('dark-mode');
        if (icon) icon.classList.replace('fa-moon', 'fa-sun');
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            const isDark = body.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            
            if (icon) {
                if (isDark) icon.classList.replace('fa-moon', 'fa-sun');
                else icon.classList.replace('fa-sun', 'fa-moon');
            }
        });
    }
});