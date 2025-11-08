Conversion POC: React TSX -> Blade + Tailwind + Vanilla JS

What I changed (POC)
- Added a Blade layout: `resources/views/layouts/app.blade.php`
- Added a navbar partial: `resources/views/partials/navbar.blade.php`
- Added a dashboard view: `resources/views/dashboard.blade.php`
- Added a minimal site JS: `public/js/site.js` (mobile menu toggle)
- Added a Tailwind-ready `resources/css/app.css` (Tailwind directives included)
- Added a `/dashboard` route in `routes/web.php` for quick testing

How to build the frontend (recommended)
1. From your project root (`c:\xampp\htdocs\hotelproject`) ensure Node is installed.
2. Install dependencies (if you maintain the React project's `package.json`, you can copy needed devDeps; otherwise run):

```powershell
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init
```

3. Configure Tailwind's `content`/`purge` in `tailwind.config.js` to include `resources/views/**/*.blade.php` and any JS files.
4. Add npm scripts to build CSS (example):

```json
"scripts": {
  "build:css": "tailwindcss -i resources/css/app.css -o public/css/app.css --minify",
  "watch:css": "tailwindcss -i resources/css/app.css -o public/css/app.css --watch"
}
```

5. Run `npm run build:css` then open `http://localhost` via your XAMPP setup (or `php artisan serve`) and visit `/dashboard`.

Quick test with built assets
- If `public/css/app.css` exists and `public/js/site.js` is present, the layout will include them automatically.
- If using Vite, ensure `@vite` calls in the layout and Vite dev server are set up.

Next steps I can take
- Convert remaining components and pages from `project/src` into Blade views and JS files.
- Add simple controllers and database scaffolding (MySQL) for reservations/rooms if you want dynamic data.
- Optionally integrate Alpine.js for reactive behavior with minimal JS.

Tell me which next step you want me to take (convert more pages, wire controllers, or generate DB migrations & seeders).