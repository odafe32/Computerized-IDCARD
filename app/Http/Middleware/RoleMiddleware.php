<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        // Check if user account is active
        if ($user->status !== 'active') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account is ' . $user->status . '. Please contact administrator.');
        }

        // Check if user has the required role
        if ($user->role !== $role) {
            // Log unauthorized access attempt
            \Log::warning('Unauthorized access attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'required_role' => $role,
                'requested_url' => $request->url(),
                'ip' => $request->ip(),
            ]);

            // Redirect based on user's actual role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access that page.');
            } else {
                return redirect()->route('student.dashboard')->with('error', 'Access denied. You do not have permission to access that page.');
            }
        }

        return $next($request);
    }
}
