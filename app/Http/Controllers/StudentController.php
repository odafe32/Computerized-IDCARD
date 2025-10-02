<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use PDF;

class StudentController extends Controller
{
    public function showDashboard()
    {
        $user = Auth::user();

        $viewData = [
            'meta_title' => 'Student Dashboard | Lexa University',
            'meta_desc' => 'Student dashboard for Lexa University Student ID Portal',
            'meta_image' => url('logo.png'),
            'user' => $user,
        ];

        return view('student.dashboard', $viewData);
    }

    /**
     * Show the student's profile
     */
    public function showProfile()
    {
        $user = Auth::user();

        $viewData = [
            'meta_title' => 'My Profile | Lexa University',
            'meta_desc' => 'View and manage your student profile',
            'meta_image' => url('logo.png'),
            'user' => $user,
        ];

        return view('student.profile.show', $viewData);
    }

    /**
     * Show the profile edit form
     */
    public function editProfile()
    {
        $user = Auth::user();

        $departments = [
            'Computer Science',
            'Engineering',
            'Business Administration',
            'Medicine',
            'Law',
            'Arts and Humanities',
            'Social Sciences',
            'Natural Sciences',
            'Education',
            'Agriculture',
        ];

        $viewData = [
            'meta_title' => 'Edit Profile | Lexa University',
            'meta_desc' => 'Edit your student profile information',
            'meta_image' => url('logo.png'),
            'user' => $user,
            'departments' => $departments,
        ];

        return view('student.profile.edit', $viewData);
    }

    /**
     * Update the student's profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'department' => 'required|string|max:255',
        ], [
            'name.required' => 'Full name is required.',
            'name.max' => 'Full name cannot exceed 255 characters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already taken.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'department.required' => 'Department is required.',
        ]);

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'department' => $request->department,
            ]);

            Log::info('Student profile updated', [
                'user_id' => $user->id,
                'matric_no' => $user->matric_no,
                'updated_fields' => $request->only(['name', 'email', 'phone', 'department']),
                'ip' => $request->ip(),
            ]);

            return redirect()->route('student.profile')
                ->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update student profile', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Update the student's profile photo
     */
    public function updatePhoto(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048', // 2MB max
        ], [
            'photo.required' => 'Please select a photo to upload.',
            'photo.image' => 'The file must be an image.',
            'photo.mimes' => 'Only JPEG, PNG, and JPG images are allowed.',
            'photo.max' => 'The photo size cannot exceed 2MB.',
        ]);

        try {
            // Delete old photo if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            // Store new photo
            $photoPath = $request->file('photo')->store('profile-photos', 'public');

            // Update user record
            $user->update(['photo' => $photoPath]);

            Log::info('Student profile photo updated', [
                'user_id' => $user->id,
                'matric_no' => $user->matric_no,
                'photo_path' => $photoPath,
                'ip' => $request->ip(),
            ]);

            return back()->with('success', 'Profile photo updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update student profile photo', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return back()->with('error', 'Failed to update profile photo. Please try again.');
        }
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        $viewData = [
            'meta_title' => 'Change Password | Lexa University',
            'meta_desc' => 'Change your account password',
            'meta_image' => url('logo.png'),
        ];

        return view('student.profile.change-password', $viewData);
    }

    /**
     * Update the student's password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ], [
            'current_password.required' => 'Current password is required.',
            'password.required' => 'New password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.'
            ]);
        }

        try {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            Log::info('Student password changed', [
                'user_id' => $user->id,
                'matric_no' => $user->matric_no,
                'ip' => $request->ip(),
            ]);

            return back()->with('success', 'Password changed successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to change student password', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return back()->with('error', 'Failed to change password. Please try again.');
        }
    }

    /**
     * Show ID card details
     */
    public function showIdCard()
    {
        $user = Auth::user();

        $viewData = [
            'meta_title' => 'My ID Card | Lexa University',
            'meta_desc' => 'View your student ID card details',
            'meta_image' => url('logo.png'),
            'user' => $user,
        ];

        return view('student.id-card.show', $viewData);
    }

    /**
     * Show ID card request form
     */
    public function showIdCardRequest()
    {
        $user = Auth::user();

        $viewData = [
            'meta_title' => 'Request ID Card | Lexa University',
            'meta_desc' => 'Request a new student ID card',
            'meta_image' => url('logo.png'),
            'user' => $user,
        ];

        return view('student.id-card.request', $viewData);
    }

    /**
     * Submit ID card request
     */
    public function submitIdCardRequest(Request $request)
    {
        // Implementation for ID card request submission
        return back()->with('success', 'ID card request submitted successfully!');
    }

    /**
     * Show ID card status
     */
    public function showIdCardStatus()
    {
        $user = Auth::user();

        $viewData = [
            'meta_title' => 'ID Card Status | Lexa University',
            'meta_desc' => 'Check your ID card request status',
            'meta_image' => url('logo.png'),
            'user' => $user,
        ];

        return view('student.id-card.status', $viewData);
    }

    /**
     * Download ID card
     */
    public function downloadIdCard()
    {
        // Implementation for ID card download
        return back()->with('info', 'ID card download feature coming soon!');
    }
}
