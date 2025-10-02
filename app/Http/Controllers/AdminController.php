<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\IdCardRequest;
use App\Models\Notification;
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

class AdminController extends Controller
{
    /**
 * Get dashboard statistics (API endpoint)
 */
public function getDashboardStats()
{
    try {
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'pending_requests' => IdCardRequest::where('status', 'pending')->count(),
            'cards_generated' => IdCardRequest::whereNotNull('generated_card_path')->count(),
            'ready_cards' => IdCardRequest::where('status', 'ready')->count(),
            'approved_requests' => IdCardRequest::where('status', 'approved')->count(),
            'rejected_requests' => IdCardRequest::where('status', 'rejected')->count(),
            'collected_cards' => IdCardRequest::where('status', 'collected')->count(),
            'active_students' => User::where('role', 'student')->where('status', 'active')->count(),
            'inactive_students' => User::where('role', 'student')->where('status', 'inactive')->count(),
            'recent_requests' => IdCardRequest::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($request) {
                    return [
                        'id' => $request->id,
                        'request_number' => $request->request_number,
                        'student_name' => $request->user->name,
                        'status' => $request->status,
                        'created_at' => $request->created_at->diffForHumans(),
                    ];
                }),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);

    } catch (\Exception $e) {
        Log::error('Failed to get dashboard stats', [
            'error' => $e->getMessage(),
            'admin_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to load dashboard statistics'
        ], 500);
    }
}

     /**
     * Generate QR code for ID card - Optimized for performance
     */
    private function generateQRCode(IdCardRequest $idCard)
    {
        // Simplified QR data for faster generation
        $qrData = $idCard->card_number . '|' . $idCard->user->name . '|' . $idCard->user->matric_no;

        try {
            // First try: Use online QR service (fastest and most reliable)
            $onlineQrPath = $this->generateOnlineQRCode($idCard, $qrData);
            if ($onlineQrPath) {
                Log::info('QR Code generated successfully using online service', [
                    'card_number' => $idCard->card_number,
                    'method' => 'online'
                ]);
                return $onlineQrPath;
            }

            // Second try: Use local SVG generation
            $generator = new \SimpleSoftwareIO\QrCode\Generator();
            $qrCode = $generator->format('svg')
                       ->size(200)  // Reduced size for faster generation
                       ->margin(1)
                       ->errorCorrection('L')  // Low error correction for speed
                       ->generate($qrData);

            $qrPath = 'qr-codes/' . $idCard->card_number . '.svg';
            Storage::disk('public')->put($qrPath, $qrCode);

            Log::info('QR Code generated successfully with local SVG', [
                'card_number' => $idCard->card_number,
                'method' => 'local_svg'
            ]);

            return $qrPath;

        } catch (\Exception $e) {
            // Final fallback: Quick and simple approach
            Log::warning('QR Code generation failed, using quick fallback', [
                'error' => $e->getMessage(),
                'card_number' => $idCard->card_number
            ]);

            return $this->generateQuickFallbackQR($idCard);
        }
    }

    /**
     * Quick fallback - create simple SVG placeholder
     */
    private function generateQuickFallbackQR(IdCardRequest $idCard)
    {
        try {
            // Create a simple SVG placeholder that looks like a QR code
            $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="150" height="150" viewBox="0 0 150 150" xmlns="http://www.w3.org/2000/svg">
    <rect width="150" height="150" fill="white" stroke="black" stroke-width="2"/>
    <!-- Corner squares -->
    <rect x="10" y="10" width="20" height="20" fill="black"/>
    <rect x="120" y="10" width="20" height="20" fill="black"/>
    <rect x="10" y="120" width="20" height="20" fill="black"/>
    <!-- Center pattern -->
    <rect x="65" y="65" width="20" height="20" fill="black"/>
    <!-- Random pattern -->
    <rect x="40" y="25" width="5" height="5" fill="black"/>
    <rect x="50" y="35" width="5" height="5" fill="black"/>
    <rect x="85" y="45" width="5" height="5" fill="black"/>
    <rect x="95" y="55" width="5" height="5" fill="black"/>
    <!-- Card info text -->
    <text x="75" y="100" text-anchor="middle" font-size="8" fill="black">' . substr($idCard->card_number, -6) . '</text>
    <text x="75" y="115" text-anchor="middle" font-size="6" fill="gray">Lexa University</text>
</svg>';

            $qrPath = 'qr-codes/' . $idCard->card_number . '.svg';
            Storage::disk('public')->put($qrPath, $svg);

            return $qrPath;

        } catch (\Exception $e) {
            // If even this fails, return null and skip QR code
            return null;
        }
    }

    /**
     * Fast online QR code generation using QR Server API
     */
    private function generateOnlineQRCode(IdCardRequest $idCard, string $qrData)
    {
        try {
            // Use QR Server API (free, fast, reliable)
            $encodedData = urlencode($qrData);
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={$encodedData}";

            // Quick download with short timeout
            $response = \Illuminate\Support\Facades\Http::timeout(2)->get($qrUrl);

            if ($response->successful()) {
                $qrPath = 'qr-codes/' . $idCard->card_number . '.png';
                Storage::disk('public')->put($qrPath, $response->body());
                return $qrPath;
            }

            return null;

        } catch (\Exception $e) {
            // Don't log errors for faster execution, just return null
            return null;
        }
    }

public function showDashboard()
{
    $statusData = [
        'pending'   => \App\Models\IdCardRequest::where('status', 'pending')->count(),
        'approved'  => \App\Models\IdCardRequest::where('status', 'approved')->count(),
        'printed'   => \App\Models\IdCardRequest::where('status', 'printed')->count(),
        'ready'     => \App\Models\IdCardRequest::where('status', 'ready')->count(),
        'collected' => \App\Models\IdCardRequest::where('status', 'collected')->count(),
        'rejected'  => \App\Models\IdCardRequest::where('status', 'rejected')->count(),
    ];

    $viewData = [
        'meta_title' => 'Admin Dashboard | Lexa University',
        'meta_desc'  => 'Admin dashboard for Lexa University',
        'meta_image' => url('logo.png'),
        'statusData' => $statusData, // ✅ add this line
    ];

    return view('admin.dashboard', $viewData);
}


    /**
     * List all ID card requests
     */
    public function listIdCards()
    {
        $requests = IdCardRequest::with(['user', 'reviewer'])
                                ->orderBy('created_at', 'desc')
                                ->paginate(20);

        $viewData = [
            'meta_title' => 'ID Card Requests | Admin',
            'meta_desc' => 'Manage student ID card requests',
            'meta_image' => url('logo.png'),
            'requests' => $requests,
        ];

        return view('admin.id-cards.index', $viewData);
    }

    /**
     * Show specific ID card request
     */

    /**
     * Approve ID card request
     */
    public function approveIdCard(Request $request, IdCardRequest $idCard)
    {
        $request->validate([
            'admin_feedback' => 'nullable|string|max:500',
        ]);

        if ($idCard->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        try {
            $idCard->update([
                'status' => 'approved',
                'admin_feedback' => $request->admin_feedback,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // Create notification for student
            Notification::notifyIdCardApproved($idCard);

            Log::info('ID card request approved', [
                'request_id' => $idCard->id,
                'request_number' => $idCard->request_number,
                'student_id' => $idCard->user_id,
                'admin_id' => Auth::id(),
                'ip' => $request->ip(),
            ]);

            return back()->with('success', 'ID card request approved successfully. Student has been notified.');

        } catch (\Exception $e) {
            Log::error('Failed to approve ID card request', [
                'request_id' => $idCard->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
                'ip' => $request->ip(),
            ]);

            return back()->with('error', 'Failed to approve request. Please try again.');
        }
    }

    /**
     * Reject ID card request
     */
    public function rejectIdCard(Request $request, IdCardRequest $idCard)
    {
        $request->validate([
            'admin_feedback' => 'required|string|max:500',
        ], [
            'admin_feedback.required' => 'Please provide a reason for rejection.',
        ]);

        if ($idCard->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        try {
            $idCard->update([
                'status' => 'rejected',
                'admin_feedback' => $request->admin_feedback,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // Create notification for student
            Notification::notifyIdCardRejected($idCard, $request->admin_feedback);

            Log::info('ID card request rejected', [
                'request_id' => $idCard->id,
                'request_number' => $idCard->request_number,
                'student_id' => $idCard->user_id,
                'admin_id' => Auth::id(),
                'reason' => $request->admin_feedback,
                'ip' => $request->ip(),
            ]);

            return back()->with('success', 'ID card request rejected. Student has been notified.');

        } catch (\Exception $e) {
            Log::error('Failed to reject ID card request', [
                'request_id' => $idCard->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
                'ip' => $request->ip(),
            ]);

            return back()->with('error', 'Failed to reject request. Please try again.');
        }
    }

    /**
     * Mark ID card as ready for collection
     */
    public function markIdCardReady(IdCardRequest $idCard)
    {
        if (!in_array($idCard->status, ['approved', 'printed'])) {
            return back()->with('error', 'Invalid request status for this action.');
        }

        try {
            $idCard->update([
                'status' => 'ready',
                'printed_at' => $idCard->printed_at ?? now(),
            ]);

            // Create notification for student
            Notification::notifyIdCardReady($idCard);

            Log::info('ID card marked as ready', [
                'request_id' => $idCard->id,
                'request_number' => $idCard->request_number,
                'student_id' => $idCard->user_id,
                'admin_id' => Auth::id(),
            ]);

            return back()->with('success', 'ID card marked as ready. Student has been notified.');

        } catch (\Exception $e) {
            Log::error('Failed to mark ID card as ready', [
                'request_id' => $idCard->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);

            return back()->with('error', 'Failed to update status. Please try again.');
        }
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

        return view('admin.profile.show', $viewData);
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

        return view('admin.profile.edit', $viewData);
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

        ], [
            'name.required' => 'Full name is required.',
            'name.max' => 'Full name cannot exceed 255 characters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already taken.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',

        ]);

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,

            ]);

            Log::info('Student profile updated', [
                'user_id' => $user->id,
                'matric_no' => $user->matric_no,
                'updated_fields' => $request->only(['name', 'email', 'phone', 'department']),
                'ip' => $request->ip(),
            ]);

            return redirect()->route('admin.profile.show')
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

        return view('admin.profile.change-password', $viewData);
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
 * List all students with filtering and search
 */
public function listUsers(Request $request)
{
    $query = User::where('role', 'student');

    // Search functionality
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('matric_no', 'like', "%{$search}%")
              ->orWhere('department', 'like', "%{$search}%");
        });
    }

    // Filter by status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter by department
    if ($request->filled('department')) {
        $query->where('department', $request->department);
    }

    // Sort options
    $sortBy = $request->get('sort', 'created_at');
    $sortOrder = $request->get('order', 'desc');
    $query->orderBy($sortBy, $sortOrder);

    $students = $query->paginate(20)->withQueryString();

    // Get departments for filter dropdown
    $departments = User::where('role', 'student')
                      ->distinct()
                      ->pluck('department')
                      ->filter()
                      ->sort()
                      ->values();

    // Get statistics
    $stats = [
        'total' => User::where('role', 'student')->count(),
        'active' => User::where('role', 'student')->where('status', 'active')->count(),
        'inactive' => User::where('role', 'student')->where('status', 'inactive')->count(),
        'suspended' => User::where('role', 'student')->where('status', 'suspended')->count(),
    ];

    $viewData = [
        'meta_title' => 'Manage Students | Admin',
        'meta_desc' => 'Manage student accounts and information',
        'meta_image' => url('logo.png'),
        'students' => $students,
        'departments' => $departments,
        'stats' => $stats,
        'filters' => $request->only(['search', 'status', 'department', 'sort', 'order']),
    ];

    return view('admin.students.index', $viewData);
}

/**
 * Show create student form
 */
public function createUser()
{
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
        'meta_title' => 'Add New Student | Admin',
        'meta_desc' => 'Add a new student to the system',
        'meta_image' => url('logo.png'),
        'departments' => $departments,
    ];

    return view('admin.students.create', $viewData);
}

/**
 * Store new student
 */
public function storeUser(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'matric_no' => 'required|string|unique:users,matric_no',
        'department' => 'required|string|max:255',
        'phone' => 'nullable|string|max:20',
        'password' => 'required|min:8|confirmed',
        'status' => 'required|in:active,inactive,suspended',
    ], [
        'name.required' => 'Student name is required.',
        'email.required' => 'Email address is required.',
        'email.unique' => 'This email address is already registered.',
        'matric_no.required' => 'Matric number is required.',
        'matric_no.unique' => 'This matric number is already registered.',
        'department.required' => 'Department is required.',
        'password.required' => 'Password is required.',
        'password.confirmed' => 'Password confirmation does not match.',
    ]);

    try {
        $student = User::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'email' => $request->email,
            'matric_no' => $request->matric_no,
            'department' => $request->department,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'status' => $request->status,
            'email_verified_at' => now(),
        ]);

        Log::info('New student created by admin', [
            'student_id' => $student->id,
            'matric_no' => $student->matric_no,
            'admin_id' => Auth::id(),
            'ip' => $request->ip(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Student created successfully!');

    } catch (\Exception $e) {
        Log::error('Failed to create student', [
            'error' => $e->getMessage(),
            'admin_id' => Auth::id(),
            'ip' => $request->ip(),
        ]);

        return back()
            ->withInput()
            ->with('error', 'Failed to create student. Please try again.');
    }
}

/**
 * Show specific student
 */
public function showUser(User $user)
{
    if ($user->role !== 'student') {
        return redirect()->route('admin.users.index')
            ->with('error', 'User not found.');
    }

    // Get student's ID card requests
    $idCardRequests = IdCardRequest::where('user_id', $user->id)
                                  ->with('reviewer')
                                  ->orderBy('created_at', 'desc')
                                  ->get();

    $viewData = [
        'meta_title' => $user->name . ' | Student Details',
        'meta_desc' => 'View student details and information',
        'meta_image' => url('logo.png'),
        'student' => $user,
        'idCardRequests' => $idCardRequests,
    ];

    return view('admin.students.show', $viewData);
}

/**
 * Show edit student form
 */
public function editUser(User $user)
{
    if ($user->role !== 'student') {
        return redirect()->route('admin.users.index')
            ->with('error', 'User not found.');
    }

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
        'meta_title' => 'Edit ' . $user->name . ' | Admin',
        'meta_desc' => 'Edit student information',
        'meta_image' => url('logo.png'),
        'student' => $user,
        'departments' => $departments,
    ];

    return view('admin.students.edit', $viewData);
}

/**
 * Update student information
 */
public function updateUser(Request $request, User $user)
{
    if ($user->role !== 'student') {
        return redirect()->route('admin.users.index')
            ->with('error', 'User not found.');
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => [
            'required',
            'email',
            Rule::unique('users')->ignore($user->id),
        ],
        'matric_no' => [
            'required',
            'string',
            Rule::unique('users')->ignore($user->id),
        ],
        'department' => 'required|string|max:255',
        'phone' => 'nullable|string|max:20',
        'status' => 'required|in:active,inactive,suspended',
        'password' => 'nullable|min:8|confirmed',
    ]);

    try {
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'matric_no' => $request->matric_no,
            'department' => $request->department,
            'phone' => $request->phone,
            'status' => $request->status,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        Log::info('Student updated by admin', [
            'student_id' => $user->id,
            'matric_no' => $user->matric_no,
            'admin_id' => Auth::id(),
            'changes' => $request->only(['name', 'email', 'matric_no', 'department', 'phone', 'status']),
            'ip' => $request->ip(),
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Student information updated successfully!');

    } catch (\Exception $e) {
        Log::error('Failed to update student', [
            'student_id' => $user->id,
            'error' => $e->getMessage(),
            'admin_id' => Auth::id(),
            'ip' => $request->ip(),
        ]);

        return back()
            ->withInput()
            ->with('error', 'Failed to update student information. Please try again.');
    }
}

/**
 * Delete student
 */
public function deleteUser(User $user)
{
    if ($user->role !== 'student') {
        return redirect()->route('admin.users.index')
            ->with('error', 'User not found.');
    }

    try {
        // Check if student has any ID card requests
        $hasRequests = IdCardRequest::where('user_id', $user->id)->exists();

        if ($hasRequests) {
            return back()->with('error', 'Cannot delete student with existing ID card requests. Please handle requests first.');
        }

        $matricNo = $user->matric_no;
        $name = $user->name;

        // Delete profile photo if exists
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->delete();

        Log::info('Student deleted by admin', [
            'student_matric_no' => $matricNo,
            'student_name' => $name,
            'admin_id' => Auth::id(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Student deleted successfully.');

    } catch (\Exception $e) {
        Log::error('Failed to delete student', [
            'student_id' => $user->id,
            'error' => $e->getMessage(),
            'admin_id' => Auth::id(),
        ]);

        return back()->with('error', 'Failed to delete student. Please try again.');
    }
}

/**
 * Activate student account
 */
public function activateUser(User $user)
{
    if ($user->role !== 'student') {
        return back()->with('error', 'User not found.');
    }

    $user->update(['status' => 'active']);

    Log::info('Student account activated', [
        'student_id' => $user->id,
        'matric_no' => $user->matric_no,
        'admin_id' => Auth::id(),
    ]);

    return back()->with('success', 'Student account activated successfully.');
}

/**
 * Deactivate student account
 */
public function deactivateUser(User $user)
{
    if ($user->role !== 'student') {
        return back()->with('error', 'User not found.');
    }

    $user->update(['status' => 'inactive']);

    Log::info('Student account deactivated', [
        'student_id' => $user->id,
        'matric_no' => $user->matric_no,
        'admin_id' => Auth::id(),
    ]);

    return back()->with('success', 'Student account deactivated successfully.');
}

/**
 * Suspend student account
 */
public function suspendUser(User $user)
{
    if ($user->role !== 'student') {
        return back()->with('error', 'User not found.');
    }

    $user->update(['status' => 'suspended']);

    Log::info('Student account suspended', [
        'student_id' => $user->id,
        'matric_no' => $user->matric_no,
        'admin_id' => Auth::id(),
    ]);

    return back()->with('success', 'Student account suspended successfully.');
}

/**
 * Reset student password
 */
public function resetUserPassword(User $user)
{
    if ($user->role !== 'student') {
        return back()->with('error', 'User not found.');
    }

    $newPassword = 'student123'; // Default password
    $user->update(['password' => Hash::make($newPassword)]);

    Log::info('Student password reset by admin', [
        'student_id' => $user->id,
        'matric_no' => $user->matric_no,
        'admin_id' => Auth::id(),
    ]);

    return back()->with('success', "Password reset successfully. New password: {$newPassword}");
}

/**
 * Search students (AJAX endpoint)
 */
public function searchUsers(Request $request)
{
    $query = User::where('role', 'student');

    if ($request->filled('q')) {
        $search = $request->q;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('matric_no', 'like', "%{$search}%");
        });
    }

    $students = $query->limit(10)->get(['id', 'name', 'email', 'matric_no']);

    return response()->json($students);
}


// Add these methods to your existing AdminController class

/**
 * Generate ID card with QR code - Optimized for speed
 */
public function generateIdCard(IdCardRequest $idCard)
{
    if (!$idCard->canBePrinted()) {
        return back()->with('error', 'ID card cannot be generated at this stage.');
    }

    try {
        // Generate card number if not exists
        if (!$idCard->card_number) {
            $idCard->update(['card_number' => IdCardRequest::generateCardNumber()]);
        }

        // Generate QR code (non-blocking - continue even if it fails)
        $qrCodePath = null;
        try {
            $qrCodePath = $this->generateQRCode($idCard);
        } catch (\Exception $e) {
            Log::warning('QR Code generation failed, continuing without QR', [
                'error' => $e->getMessage(),
                'card_number' => $idCard->card_number
            ]);
        }

        // Generate ID card as image (better layout control)
        $imagePath = $this->generateIDCardImage($idCard, $qrCodePath);

        // Update request
        $idCard->update([
            'qr_code_path' => $qrCodePath,
            'generated_card_path' => $imagePath,
            'status' => 'printed',
            'printed_at' => now(),
        ]);

        // Create notification for student (non-blocking)
        try {
            Notification::notifyIdCardReady($idCard);
        } catch (\Exception $e) {
            // Don't fail the whole process if notification fails
            Log::warning('Notification failed', ['error' => $e->getMessage()]);
        }

        Log::info('ID card generated successfully', [
            'request_id' => $idCard->id,
            'card_number' => $idCard->card_number,
            'has_qr' => $qrCodePath ? 'yes' : 'no',
            'admin_id' => Auth::id(),
        ]);

        return back()->with('success', 'ID card generated successfully!');

    } catch (\Exception $e) {
        Log::error('Failed to generate ID card', [
            'request_id' => $idCard->id,
            'error' => $e->getMessage(),
            'admin_id' => Auth::id(),
        ]);

        return back()->with('error', 'Failed to generate ID card. Please try again.');
    }
}



    /**
     * Generate ID card as image - Much better control over layout
     */
    private function generateIDCardImage(IdCardRequest $idCard, $qrCodePath = null)
    {
        try {
            // ID Card dimensions (convert mm to pixels at 300 DPI)
            $cardWidth = 1012;  // 85.6mm at 300 DPI
            $cardHeight = 639;  // 53.98mm at 300 DPI

            // Create main canvas
            $canvas = imagecreatetruecolor($cardWidth, $cardHeight);

            // Colors
            $white = imagecolorallocate($canvas, 255, 255, 255);
            $blue = imagecolorallocate($canvas, 26, 54, 93);
            $lightBlue = imagecolorallocate($canvas, 45, 90, 135);
            $gray = imagecolorallocate($canvas, 102, 102, 102);
            $darkGray = imagecolorallocate($canvas, 51, 51, 51);
            $lightGray = imagecolorallocate($canvas, 240, 240, 240);

            // Fill background
            imagefill($canvas, 0, 0, $white);

            // Add border
            imagerectangle($canvas, 0, 0, $cardWidth-1, $cardHeight-1, $blue);
            imagerectangle($canvas, 1, 1, $cardWidth-2, $cardHeight-2, $blue);

            // Header background gradient (simulate with rectangles)
            $headerHeight = 142; // 12mm at 300 DPI
            for ($i = 0; $i < $headerHeight; $i++) {
                $ratio = $i / $headerHeight;
                $r = (int)(26 + ($ratio * 19)); // 26 to 45
                $g = (int)(54 + ($ratio * 36)); // 54 to 90
                $b = (int)(93 + ($ratio * 42)); // 93 to 135
                $color = imagecolorallocate($canvas, $r, $g, $b);
                imageline($canvas, 0, $i, $cardWidth, $i, $color);
            }

            // University Name
            $fontFile = $this->getFontPath();
            if ($fontFile && file_exists($fontFile)) {
                imagettftext($canvas, 24, 0, 120, 60, $white, $fontFile, 'LEXA UNIVERSITY');
                imagettftext($canvas, 14, 0, 120, 90, $white, $fontFile, 'STUDENT ID CARD');
            } else {
                imagestring($canvas, 5, 120, 30, 'LEXA UNIVERSITY', $white);
                imagestring($canvas, 3, 120, 60, 'STUDENT ID CARD', $white);
            }

            // Student photo area
            $photoX = 35;
            $photoY = 170;
            $photoWidth = 236;  // 20mm
            $photoHeight = 295; // 25mm

            imagerectangle($canvas, $photoX-1, $photoY-1, $photoX+$photoWidth, $photoY+$photoHeight, $darkGray);

            // Load and resize student photo
            $photoPath = null;
            if ($idCard->user->photo) {
                $photoPath = storage_path('app/public/' . $idCard->user->photo);
            } elseif (isset($idCard->photo_url)) {
                $photoPath = public_path($idCard->photo_url);
            }

            if ($photoPath && file_exists($photoPath)) {
                $studentPhoto = $this->loadAndResizeImage($photoPath, $photoWidth, $photoHeight);
                if ($studentPhoto) {
                    imagecopy($canvas, $studentPhoto, $photoX, $photoY, 0, 0, $photoWidth, $photoHeight);
                    imagedestroy($studentPhoto);
                }
            } else {
                // Photo placeholder
                imagefilledrectangle($canvas, $photoX, $photoY, $photoX+$photoWidth, $photoY+$photoHeight, $lightGray);
                if ($fontFile) {
                    imagettftext($canvas, 12, 0, $photoX+60, $photoY+150, $gray, $fontFile, 'PHOTO');
                } else {
                    imagestring($canvas, 3, $photoX+80, $photoY+140, 'PHOTO', $gray);
                }
            }

            // Student information
            $infoX = 300;
            $infoY = 180;

            if ($fontFile) {
                // Student name (larger font)
                imagettftext($canvas, 20, 0, $infoX, $infoY, $blue, $fontFile, strtoupper($idCard->user->name));

                // Details
                $details = [
                    'ID: ' . ($idCard->card_number ?? 'N/A'),
                    'Matric: ' . $idCard->user->matric_no,
                    'Dept: ' . substr($idCard->user->department, 0, 25),
                    'Email: ' . substr($idCard->user->email, 0, 28),
                ];

                if ($idCard->user->phone) {
                    $details[] = 'Phone: ' . $idCard->user->phone;
                }

                $lineHeight = 30;
                foreach ($details as $i => $detail) {
                    imagettftext($canvas, 12, 0, $infoX, $infoY + 50 + ($i * $lineHeight), $darkGray, $fontFile, $detail);
                }
            } else {
                // Fallback to built-in fonts
                imagestring($canvas, 4, $infoX, $infoY, strtoupper($idCard->user->name), $blue);
                imagestring($canvas, 3, $infoX, $infoY + 30, 'ID: ' . ($idCard->card_number ?? 'N/A'), $darkGray);
                imagestring($canvas, 3, $infoX, $infoY + 50, 'Matric: ' . $idCard->user->matric_no, $darkGray);
                imagestring($canvas, 3, $infoX, $infoY + 70, 'Dept: ' . substr($idCard->user->department, 0, 20), $darkGray);
                imagestring($canvas, 3, $infoX, $infoY + 90, 'Email: ' . substr($idCard->user->email, 0, 25), $darkGray);
                if ($idCard->user->phone) {
                    imagestring($canvas, 3, $infoX, $infoY + 110, 'Phone: ' . $idCard->user->phone, $darkGray);
                }
            }

            // QR Code
            if ($qrCodePath && file_exists(storage_path('app/public/' . $qrCodePath))) {
                $qrSize = 177; // 15mm
                $qrX = $cardWidth - $qrSize - 35;
                $qrY = 178;

                $qrImage = $this->loadAndResizeImage(storage_path('app/public/' . $qrCodePath), $qrSize, $qrSize);
                if ($qrImage) {
                    // QR background
                    imagefilledrectangle($canvas, $qrX-2, $qrY-2, $qrX+$qrSize+1, $qrY+$qrSize+1, $white);
                    imagerectangle($canvas, $qrX-2, $qrY-2, $qrX+$qrSize+1, $qrY+$qrSize+1, $gray);

                    imagecopy($canvas, $qrImage, $qrX, $qrY, 0, 0, $qrSize, $qrSize);
                    imagedestroy($qrImage);
                }
            }

            // Footer
            $footerY = $cardHeight - 40;
            if ($fontFile) {
                imagettftext($canvas, 10, 0, 35, $footerY, $gray, $fontFile, $idCard->card_number ?? 'N/A');
                $validity = now()->format('m/Y') . ' - ' . now()->addYears(4)->format('m/Y');
                imagettftext($canvas, 10, 0, $cardWidth - 200, $footerY, $gray, $fontFile, $validity);
            } else {
                imagestring($canvas, 2, 35, $footerY - 15, $idCard->card_number ?? 'N/A', $gray);
                $validity = now()->format('m/Y') . ' - ' . now()->addYears(4)->format('m/Y');
                imagestring($canvas, 2, $cardWidth - 150, $footerY - 15, $validity, $gray);
            }

            // Save image
            $imagePath = 'id-cards/' . $idCard->card_number . '.png';
            $fullPath = storage_path('app/public/' . $imagePath);

            // Ensure directory exists
            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            imagepng($canvas, $fullPath, 9); // High quality PNG
            imagedestroy($canvas);

            return $imagePath;

        } catch (\Exception $e) {
            Log::error('Failed to generate ID card image', [
                'error' => $e->getMessage(),
                'card_number' => $idCard->card_number
            ]);

            return null;
        }
    }

    /**
     * Get font file path for TTF fonts
     */
    private function getFontPath()
    {
        $possiblePaths = [
            public_path('fonts/arial.ttf'),
            public_path('assets/fonts/arial.ttf'),
            storage_path('fonts/arial.ttf'),
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null; // Fallback to built-in fonts
    }

    /**
     * Load and resize image for ID card
     */
    private function loadAndResizeImage($imagePath, $newWidth, $newHeight)
    {
        try {
            $imageInfo = getimagesize($imagePath);
            if (!$imageInfo) return null;

            $mime = $imageInfo['mime'];

            switch ($mime) {
                case 'image/jpeg':
                    $source = imagecreatefromjpeg($imagePath);
                    break;
                case 'image/png':
                    $source = imagecreatefrompng($imagePath);
                    break;
                case 'image/gif':
                    $source = imagecreatefromgif($imagePath);
                    break;
                case 'image/svg+xml':
                    // For SVG (like QR codes), we'll try to handle it
                    return $this->createImageFromSVG($imagePath, $newWidth, $newHeight);
                default:
                    return null;
            }

            if (!$source) return null;

            $originalWidth = imagesx($source);
            $originalHeight = imagesy($source);

            $resized = imagecreatetruecolor($newWidth, $newHeight);

            // Handle transparency for PNG
            if ($mime === 'image/png') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
            }

            imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            imagedestroy($source);

            return $resized;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Create image from SVG (for QR codes)
     */
    private function createImageFromSVG($svgPath, $width, $height)
    {
        try {
            $svg = file_get_contents($svgPath);
            if (!$svg) return null;

            // Simple SVG to image conversion for QR codes
            // This is a basic implementation - you might want to use a library like librsvg
            $image = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($image, 255, 255, 255);
            $black = imagecolorallocate($image, 0, 0, 0);

            imagefill($image, 0, 0, $white);

            // Parse simple SVG rectangles (basic QR code parsing)
            if (preg_match_all('/<rect[^>]*x="([^"]+)"[^>]*y="([^"]+)"[^>]*width="([^"]+)"[^>]*height="([^"]+)"[^>]*fill="black"/', $svg, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $x = (int)($match[1] * $width / 150); // Scale to target size
                    $y = (int)($match[2] * $height / 150);
                    $w = (int)($match[3] * $width / 150);
                    $h = (int)($match[4] * $height / 150);

                    imagefilledrectangle($image, $x, $y, $x+$w, $y+$h, $black);
                }
            }

            return $image;

        } catch (\Exception $e) {
            return null;
        }
    }



/**
 * Preview ID card before printing
 */
public function previewIdCard(IdCardRequest $idCard)
{
    if (!$idCard->canBePrinted()) {
        return back()->with('error', 'ID card cannot be previewed at this stage.');
    }

    // Generate temporary card number for preview if not exists
    $cardNumber = $idCard->card_number ?: IdCardRequest::generateCardNumber();

    $data = [
        'request' => $idCard,
        'student' => $idCard->user,
        'card_number' => $cardNumber,
        'university_logo' => public_path('logo.png'),
        'generated_at' => now(),
        'is_preview' => true,
    ];

    return view('admin.id-cards.preview', $data);
}

/**
 * Mark ID card as printed
 */
public function markIdCardPrinted(IdCardRequest $idCard)
{
    if (!in_array($idCard->status, ['approved'])) {
        return back()->with('error', 'Invalid request status for this action.');
    }

    try {
        $idCard->update([
            'status' => 'printed',
            'printed_at' => now(),
        ]);

        Log::info('ID card marked as printed', [
            'request_id' => $idCard->id,
            'card_number' => $idCard->card_number,
            'admin_id' => Auth::id(),
        ]);

        return back()->with('success', 'ID card marked as printed successfully.');

    } catch (\Exception $e) {
        Log::error('Failed to mark ID card as printed', [
            'request_id' => $idCard->id,
            'error' => $e->getMessage(),
            'admin_id' => Auth::id(),
        ]);

        return back()->with('error', 'Failed to update status. Please try again.');
    }
}

/**
 * Mark ID card as collected
 */
public function markIdCardCollected(Request $request, IdCardRequest $idCard)
{
    if (!$idCard->canBeCollected()) {
        return back()->with('error', 'ID card is not ready for collection.');
    }

    $request->validate([
        'collected_by' => 'required|string|max:255',
        'collection_notes' => 'nullable|string|max:500',
    ]);

    try {
        $idCard->update([
            'status' => 'collected',
            'collected_at' => now(),
            'collected_by' => $request->collected_by,
            'collection_notes' => $request->collection_notes,
        ]);

        Log::info('ID card marked as collected', [
            'request_id' => $idCard->id,
            'card_number' => $idCard->card_number,
            'collected_by' => $request->collected_by,
            'admin_id' => Auth::id(),
        ]);

        return back()->with('success', 'ID card marked as collected successfully.');

    } catch (\Exception $e) {
        Log::error('Failed to mark ID card as collected', [
            'request_id' => $idCard->id,
            'error' => $e->getMessage(),
            'admin_id' => Auth::id(),
        ]);

        return back()->with('error', 'Failed to update status. Please try again.');
    }
}

/**
 * Bulk approve ID card requests
 */
public function bulkApproveIdCards(Request $request)
{
    $request->validate([
        'request_ids' => 'required|array',
        'request_ids.*' => 'exists:id_card_requests,id',
        'admin_feedback' => 'nullable|string|max:500',
    ]);

    try {
        $requests = IdCardRequest::whereIn('id', $request->request_ids)
                                ->where('status', 'pending')
                                ->get();

        $approvedCount = 0;
        foreach ($requests as $idCardRequest) {
            $idCardRequest->update([
                'status' => 'approved',
                'admin_feedback' => $request->admin_feedback,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // Create notification for student
            Notification::notifyIdCardApproved($idCardRequest);
            $approvedCount++;
        }

        Log::info('Bulk ID card requests approved', [
            'approved_count' => $approvedCount,
            'admin_id' => Auth::id(),
            'request_ids' => $request->request_ids,
        ]);

        return back()->with('success', "{$approvedCount} ID card requests approved successfully.");

    } catch (\Exception $e) {
        Log::error('Failed to bulk approve ID card requests', [
            'error' => $e->getMessage(),
            'admin_id' => Auth::id(),
        ]);

        return back()->with('error', 'Failed to approve requests. Please try again.');
    }
}

/**
 * Bulk reject ID card requests
 */
public function bulkRejectIdCards(Request $request)
{
    $request->validate([
        'request_ids' => 'required|array',
        'request_ids.*' => 'exists:id_card_requests,id',
        'admin_feedback' => 'required|string|max:500',
    ]);

    try {
        $requests = IdCardRequest::whereIn('id', $request->request_ids)
                                ->where('status', 'pending')
                                ->get();

        $rejectedCount = 0;
        foreach ($requests as $idCardRequest) {
            $idCardRequest->update([
                'status' => 'rejected',
                'admin_feedback' => $request->admin_feedback,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // Create notification for student
            Notification::notifyIdCardRejected($idCardRequest, $request->admin_feedback);
            $rejectedCount++;
        }

        Log::info('Bulk ID card requests rejected', [
            'rejected_count' => $rejectedCount,
            'admin_id' => Auth::id(),
            'request_ids' => $request->request_ids,
        ]);

        return back()->with('success', "{$rejectedCount} ID card requests rejected successfully.");

    } catch (\Exception $e) {
        Log::error('Failed to bulk reject ID card requests', [
            'error' => $e->getMessage(),
            'admin_id' => Auth::id(),
        ]);

        return back()->with('error', 'Failed to reject requests. Please try again.');
    }
}

/**
 * Bulk mark ID cards as ready
 */
public function bulkMarkReady(Request $request)
{
    $request->validate([
        'request_ids' => 'required|array',
        'request_ids.*' => 'exists:id_card_requests,id',
    ]);

    try {
        $requests = IdCardRequest::whereIn('id', $request->request_ids)
                                ->whereIn('status', ['approved', 'printed'])
                                ->get();

        $readyCount = 0;
        foreach ($requests as $idCardRequest) {
            $idCardRequest->update([
                'status' => 'ready',
                'printed_at' => $idCardRequest->printed_at ?? now(),
            ]);

            // Create notification for student
            Notification::notifyIdCardReady($idCardRequest);
            $readyCount++;
        }

        Log::info('Bulk ID cards marked as ready', [
            'ready_count' => $readyCount,
            'admin_id' => Auth::id(),
            'request_ids' => $request->request_ids,
        ]);

        return back()->with('success', "{$readyCount} ID cards marked as ready successfully.");

    } catch (\Exception $e) {
        Log::error('Failed to bulk mark ID cards as ready', [
            'error' => $e->getMessage(),
            'admin_id' => Auth::id(),
        ]);

        return back()->with('error', 'Failed to update status. Please try again.');
    }
}

/**
 * Get ID card statistics (API endpoint)
 */
public function getIdCardStats()
{
    $stats = [
        'total_requests' => IdCardRequest::count(),
        'pending_requests' => IdCardRequest::where('status', 'pending')->count(),
        'approved_requests' => IdCardRequest::where('status', 'approved')->count(),
        'rejected_requests' => IdCardRequest::where('status', 'rejected')->count(),
        'printed_cards' => IdCardRequest::where('status', 'printed')->count(),
        'ready_cards' => IdCardRequest::where('status', 'ready')->count(),
        'collected_cards' => IdCardRequest::where('status', 'collected')->count(),
        'recent_requests' => IdCardRequest::with('user')
                                         ->orderBy('created_at', 'desc')
                                         ->limit(5)
                                         ->get(),
    ];

    return response()->json($stats);
}

/**
 * Show specific ID card request
 */
public function showIdCard(IdCardRequest $idCard)
{
    // Load relationships
    $idCard->load(['user', 'reviewer']);

    $viewData = [
        'meta_title' => 'ID Card Request #' . $idCard->request_number . ' | Admin',
        'meta_desc' => 'View ID card request details',
        'meta_image' => url('logo.png'),
        'request' => $idCard,
    ];

    return view('admin.id-cards.show', $viewData);
}

/**
 * Download generated ID card PDF
 */
public function downloadIdCard(IdCardRequest $idCard)
{
    // Check if ID card has been generated
    if (!$idCard->generated_card_path) {
        return back()->with('error', 'ID card has not been generated yet.');
    }

    // Check if file exists
    if (!Storage::disk('public')->exists($idCard->generated_card_path)) {
        return back()->with('error', 'ID card file not found. Please regenerate the card.');
    }

    try {
        // Get file path
        $filePath = storage_path('app/public/' . $idCard->generated_card_path);

        // Determine file type and generate download filename
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        $downloadName = 'ID_Card_' . $idCard->card_number . '_' . str_replace(' ', '_', $idCard->user->name) . '.' . $fileExtension;

        // Set appropriate content type
        $contentType = $fileExtension === 'png' ? 'image/png' :
                      ($fileExtension === 'jpg' || $fileExtension === 'jpeg' ? 'image/jpeg' : 'application/pdf');

        // Log download activity
        Log::info('ID card downloaded', [
            'request_id' => $idCard->id,
            'card_number' => $idCard->card_number,
            'student_id' => $idCard->user_id,
            'file_type' => $fileExtension,
            'admin_id' => Auth::id(),
            'ip' => request()->ip(),
        ]);

        // Return file download response
        return response()->download($filePath, $downloadName, [
            'Content-Type' => $contentType,
        ]);

    } catch (\Exception $e) {
        Log::error('Failed to download ID card', [
            'request_id' => $idCard->id,
            'error' => $e->getMessage(),
            'admin_id' => Auth::id(),
            'ip' => request()->ip(),
        ]);

        return back()->with('error', 'Failed to download ID card. Please try again.');
    }
}

/**
 * Download QR code image
 */
public function downloadQRCode(IdCardRequest $idCard)
{
    // Check if QR code has been generated
    if (!$idCard->qr_code_path) {
        return back()->with('error', 'QR code has not been generated yet.');
    }

    // Check if file exists
    if (!Storage::disk('public')->exists($idCard->qr_code_path)) {
        return back()->with('error', 'QR code file not found. Please regenerate the card.');
    }

    try {
        // Get file path
        $filePath = storage_path('app/public/' . $idCard->qr_code_path);

        // Generate download filename
        $downloadName = 'QR_Code_' . $idCard->card_number . '.png';

        // Log download activity
        Log::info('QR code downloaded', [
            'request_id' => $idCard->id,
            'card_number' => $idCard->card_number,
            'admin_id' => Auth::id(),
            'ip' => request()->ip(),
        ]);

        // Return file download response
        return response()->download($filePath, $downloadName, [
            'Content-Type' => 'image/png',
        ]);

    } catch (\Exception $e) {
        Log::error('Failed to download QR code', [
            'request_id' => $idCard->id,
            'error' => $e->getMessage(),
            'admin_id' => Auth::id(),
        ]);

        return back()->with('error', 'Failed to download QR code. Please try again.');
    }
}

/**
 * Bulk download ID cards as ZIP
 */
public function bulkDownloadIdCards(Request $request)
{
    $request->validate([
        'request_ids' => 'required|array',
        'request_ids.*' => 'exists:id_card_requests,id',
    ]);

    try {
        $requests = IdCardRequest::whereIn('id', $request->request_ids)
                                ->whereNotNull('generated_card_path')
                                ->with('user')
                                ->get();

        if ($requests->isEmpty()) {
            return back()->with('error', 'No generated ID cards found for the selected requests.');
        }

        // Create temporary ZIP file
        $zipFileName = 'ID_Cards_Bulk_' . now()->format('Y-m-d_H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
            return back()->with('error', 'Failed to create ZIP file.');
        }

        foreach ($requests as $idCardRequest) {
            $filePath = storage_path('app/public/' . $idCardRequest->generated_card_path);

            if (file_exists($filePath)) {
                $fileName = 'ID_Card_' . $idCardRequest->card_number . '_' . str_replace(' ', '_', $idCardRequest->user->name) . '.pdf';
                $zip->addFile($filePath, $fileName);
            }
        }

        $zip->close();

        // Log bulk download
        Log::info('Bulk ID cards downloaded', [
            'request_ids' => $request->request_ids,
            'count' => $requests->count(),
            'admin_id' => Auth::id(),
            'ip' => request()->ip(),
        ]);

        // Return ZIP file and delete after download
        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);

    } catch (\Exception $e) {
        Log::error('Failed to bulk download ID cards', [
            'error' => $e->getMessage(),
            'admin_id' => Auth::id(),
        ]);

        return back()->with('error', 'Failed to create bulk download. Please try again.');
    }
}
}



