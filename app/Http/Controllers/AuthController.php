<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\LoginNotification;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    public function showLogin()
    {
        $viewData = [
           'meta_title'=> 'Login | Lexa University',
           'meta_desc'=> 'A Laravel-based web application that automates the process of student ID card requests and issuance. Students can submit their details, upload photos, and track request statuses online, while administrators can review applications, approve or reject requests, generate ID cards with QR codes, and manage records efficiently.',
           'meta_image'=> url('logo.png'),
        ];

        return view('auth.login', $viewData);
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ], [
            'login_id.required' => 'Please enter your Matric Number or Email.',
            'password.required' => 'Please enter your password.',
        ]);

        $loginId = $request->login_id;
        $password = $request->password;

        // Determine if login_id is email or matric number
        $user = null;

        if (filter_var($loginId, FILTER_VALIDATE_EMAIL)) {
            // It's an email - search by email (typically admin)
            $user = User::where('email', $loginId)->first();
            $loginType = 'email';
        } else {
            // It's not an email - treat as matric number (typically student)
            $user = User::where('matric_no', $loginId)->first();
            $loginType = 'matric_no';
        }

        // If user not found or password doesn't match
        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'login_id' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if user account is active
        if ($user->status !== 'active') {
            $statusMessage = match($user->status) {
                'inactive' => 'Your account is inactive. Please contact administrator to activate your account.',
                'suspended' => 'Your account has been suspended. Please contact administrator for assistance.',
                default => 'Your account is ' . $user->status . '. Please contact administrator.',
            };

            throw ValidationException::withMessages([
                'login_id' => [$statusMessage],
            ]);
        }

        // Additional role-based validation
        if ($loginType === 'email' && $user->role !== 'admin') {
            throw ValidationException::withMessages([
                'login_id' => ['Email login is only available for administrators. Students should use their Matric Number.'],
            ]);
        }

        if ($loginType === 'matric_no' && $user->role !== 'student') {
            throw ValidationException::withMessages([
                'login_id' => ['Matric Number login is only available for students. Administrators should use their Email.'],
            ]);
        }

        // Update last login information
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Log the user in
        Auth::login($user, $request->boolean('remember'));

        // Log successful login
        Log::info('User logged in', [
            'user_id' => $user->id,
            'email' => $user->email,
            'matric_no' => $user->matric_no,
            'role' => $user->role,
            'login_type' => $loginType,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Send login notification email (optional)
        try {
            // Mail::to($user->email)->send(new LoginNotification($user, $request->ip()));
        } catch (\Exception $e) {
            Log::warning('Failed to send login notification email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Set success message
        $welcomeMessage = $user->role === 'admin'
            ? 'Welcome back, Administrator!'
            : 'Welcome back, ' . $user->name . '!';

        // Redirect based on user role
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', $welcomeMessage);
        } else {
            return redirect()->intended(route('student.dashboard'))
                ->with('success', $welcomeMessage);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            Log::info('User logged out', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'ip' => $request->ip(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    public function showForgotPassword()
    {
        $viewData = [
           'meta_title'=> 'Forgot Password | Lexa University',
           'meta_desc'=> 'Reset your password for Lexa University Student ID Portal',
           'meta_image'=> url('logo.png'),
        ];

        return view('auth.forgot', $viewData);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'We could not find a user with that email address.',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => 'Password reset link sent to your email address.'])
                    : back()->withErrors(['email' => 'Unable to send password reset link. Please try again.']);
    }

    public function showResetForm(Request $request, $token = null)
    {
        $viewData = [
           'meta_title'=> 'Reset Password | Lexa University',
           'meta_desc'=> 'Reset your password for Lexa University Student ID Portal',
           'meta_image'=> url('logo.png'),
           'token' => $token,
           'email' => $request->email,
        ];

        return view('auth.reset', $viewData);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', 'Your password has been reset successfully.')
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
