// Simple sidebar behavior (if we later add collapse/expand)
// This is a placeholder for more advanced interactions.

console.log('Sidebar JS loaded');

// Example: toggle sidebar visibility on small screens
(function () {
    const toggleBtn = document.getElementById('mobile-menu-button');
    const sidebar = document.querySelector('aside');
    if (!toggleBtn || !sidebar) return;

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('hidden');
    });
})();