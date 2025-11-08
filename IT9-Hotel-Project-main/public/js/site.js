// Minimal site JavaScript for POC (converted from React interactions)

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('mobile-menu-button');
    const menu = document.getElementById('mobile-menu');

    if (btn && menu) {
        btn.addEventListener('click', function () {
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
            }
        });
    }
});