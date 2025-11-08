<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Receptionist Middleware
 * 
 * This middleware ensures that only users with the 'receptionist' role
 * can access specific pages in the Hotel Reservation Management System.
 * 
 * Allowed pages for receptionists:
 * - Dashboard
 * - Reservations
 * - Rooms
 * - Guests
 * - Reports
 * - Settings
 */
class ReceptionistMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated
        if (!auth()->check()) {
            // Redirect to login if not authenticated
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // Get the authenticated user
        $user = auth()->user();

        // Check if the user has the 'receptionist' role
        if ($user->role !== 'receptionist') {
            // User does not have receptionist role - deny access
            // Return 403 Forbidden response
            abort(403, 'Unauthorized. Only receptionists can access this page.');
        }

        // Check if the user account is active
        if (!$user->is_active) {
            // Inactive account - deny access
            abort(403, 'Your account has been deactivated. Please contact an administrator.');
        }

        // User is authenticated, has receptionist role, and is active
        // Allow the request to proceed
        return $next($request);
    }
}
