import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Dark mode toggle functionality
(function() {
    // Check for saved theme preference or default to light mode
    const theme = localStorage.getItem('theme') || 'light';
    
    function updateIcons(isDark) {
        const sunIcons = document.querySelectorAll('#sun-icon, #sun-icon-mobile');
        const moonIcons = document.querySelectorAll('#moon-icon, #moon-icon-mobile');
        
        if (isDark) {
            sunIcons.forEach(icon => icon.classList.add('hidden'));
            moonIcons.forEach(icon => icon.classList.remove('hidden'));
        } else {
            sunIcons.forEach(icon => icon.classList.remove('hidden'));
            moonIcons.forEach(icon => icon.classList.add('hidden'));
        }
    }
    
    if (theme === 'dark') {
        document.documentElement.classList.add('dark');
        updateIcons(true);
    } else {
        document.documentElement.classList.remove('dark');
        updateIcons(false);
    }
    
    // Function to toggle theme
    window.toggleTheme = function() {
        const html = document.documentElement;
        const isDark = html.classList.contains('dark');
        
        if (isDark) {
            html.classList.remove('dark');
            localStorage.setItem('theme', 'light');
            updateIcons(false);
        } else {
            html.classList.add('dark');
            localStorage.setItem('theme', 'dark');
            updateIcons(true);
        }
    };
    
    // Update icons on page load
    updateIcons(theme === 'dark');
})();

Alpine.start();
