Auth setup (POC)

What I added
- `app/Http/Controllers/AuthController.php` with show/login/register/logout methods
- Routes in `routes/web.php` for login (GET/POST), register (GET/POST), and logout (POST)
- Updated `resources/views/login.blade.php` and `resources/views/register.blade.php` to use server-side forms and CSRF
- Navbar updated to show logout for authenticated users

Steps to enable and test server-side auth
1. Ensure your `.env` has correct database connection (MySQL) and run migrations:

```powershell
# from project root
php artisan migrate
```

2. Start the Laravel server:

```powershell
php artisan serve
```

3. Visit http://127.0.0.1:8000/register to create a user, then you should be redirected to /dashboard.
4. To log out use the Logout button in the navbar.

Notes & next steps
- This is a minimal, hand-rolled auth controller for POC. For production, install Laravel Breeze or Fortify to get hardened authentication flows, email verification, password resets, etc.
- If you prefer Breeze: run `composer require laravel/breeze --dev` then `php artisan breeze:install` and follow instructions.

