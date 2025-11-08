import './bootstrap';

// Minimal app entry for Vite
console.log('App JS loaded (Vite)');

// Optionally include our site JS
try {
	// import('../js/site.js'); // Removed: site.js is loaded directly in Blade layout
} catch (e) {
	// site.js may be loaded directly in layout; ignore if missing
}
