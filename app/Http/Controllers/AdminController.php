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
    public function showDashboard()
    {
        $viewData = [
            'meta_title' => 'Admin Dashboard | Lexa University',
            'meta_desc' => 'Admin dashboard for Lexa University',
            'meta_image' => url('logo.png'),
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
    public function showIdCard(IdCardRequest $idCard)
    {
        $idCard->load(['user', 'reviewer']);

        $viewData = [
            'meta_title' => 'ID Card Request Details | Admin',
            'meta_desc' => 'View ID card request details',
            'meta_image' => url('logo.png'),
            'request' => $idCard,
        ];

        return view('admin.id-cards.show', $viewData);
    }

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
}
