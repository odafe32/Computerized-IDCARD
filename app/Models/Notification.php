<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Notification extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'icon',
        'related_type',
        'related_id',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid();
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function related()
    {
        return $this->morphTo();
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function getIconClassAttribute()
    {
        if ($this->icon) {
            return $this->icon;
        }

        return match($this->type) {
            'success' => 'mdi-check-circle',
            'info' => 'mdi-information',
            'warning' => 'mdi-alert',
            'danger' => 'mdi-alert-circle',
            default => 'mdi-bell',
        };
    }

    public function getBadgeClassAttribute()
    {
        return match($this->type) {
            'success' => 'bg-success',
            'info' => 'bg-info',
            'warning' => 'bg-warning',
            'danger' => 'bg-danger',
            default => 'bg-primary',
        };
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeForAdmins($query)
    {
        return $query->whereHas('user', function($q) {
            $q->where('role', 'admin');
        });
    }

    public function scopeForStudents($query)
    {
        return $query->whereHas('user', function($q) {
            $q->where('role', 'student');
        });
    }

    // Static methods for creating notifications
    public static function createForUser($userId, $title, $message, $type = 'info', $options = [])
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => $options['icon'] ?? null,
            'related_type' => $options['related_type'] ?? null,
            'related_id' => $options['related_id'] ?? null,
            'data' => $options['data'] ?? null,
        ]);
    }

    public static function createForAllAdmins($title, $message, $type = 'info', $options = [])
    {
        $admins = User::where('role', 'admin')->where('status', 'active')->get();
        $notifications = [];

        foreach ($admins as $admin) {
            $notifications[] = self::createForUser(
                $admin->id,
                $title,
                $message,
                $type,
                $options
            );
        }

        return $notifications;
    }

    // ========================================
    // STUDENT NOTIFICATION METHODS
    // ========================================

    public static function notifyIdCardApproved($request)
    {
        return self::createForUser(
            $request->user_id,
            'ID Card Request Approved',
            "Your ID card request #{$request->request_number} has been approved and is being processed.",
            'success',
            [
                'icon' => 'mdi-check-circle',
                'related_type' => IdCardRequest::class,
                'related_id' => $request->id,
                'data' => [
                    'request_number' => $request->request_number,
                    'action_url' => route('student.id-card.status'),
                ]
            ]
        );
    }

    public static function notifyIdCardRejected($request, $reason = null)
    {
        $message = "Your ID card request #{$request->request_number} has been rejected.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }

        return self::createForUser(
            $request->user_id,
            'ID Card Request Rejected',
            $message,
            'danger',
            [
                'icon' => 'mdi-close-circle',
                'related_type' => IdCardRequest::class,
                'related_id' => $request->id,
                'data' => [
                    'request_number' => $request->request_number,
                    'reason' => $reason,
                    'action_url' => route('student.id-card.status'),
                ]
            ]
        );
    }

    public static function notifyIdCardReady($request)
    {
        return self::createForUser(
            $request->user_id,
            'ID Card Ready for Download',
            "Your ID card #{$request->request_number} is ready! You can now download it from your dashboard.",
            'success',
            [
                'icon' => 'mdi-download',
                'related_type' => IdCardRequest::class,
                'related_id' => $request->id,
                'data' => [
                    'request_number' => $request->request_number,
                    'action_url' => route('student.id-card.show'),
                ]
            ]
        );
    }

    // ========================================
    // ADMIN NOTIFICATION METHODS
    // ========================================

    public static function notifyAdminsNewIdCardRequest($request)
    {
        return self::createForAllAdmins(
            'New ID Card Request',
            "Student {$request->user->name} ({$request->user->matric_no}) has submitted a new ID card request #{$request->request_number}.",
            'info',
            [
                'icon' => 'mdi-card-account-details',
                'related_type' => IdCardRequest::class,
                'related_id' => $request->id,
                'data' => [
                    'request_number' => $request->request_number,
                    'student_name' => $request->user->name,
                    'student_matric' => $request->user->matric_no,
                    'reason' => $request->reason,
                    'action_url' => route('admin.id-cards.show', $request->id),
                ]
            ]
        );
    }

    public static function notifyAdminsStudentRegistered($user)
    {
        return self::createForAllAdmins(
            'New Student Registration',
            "A new student {$user->name} ({$user->matric_no}) has registered in the system.",
            'info',
            [
                'icon' => 'mdi-account-plus',
                'related_type' => User::class,
                'related_id' => $user->id,
                'data' => [
                    'student_name' => $user->name,
                    'student_matric' => $user->matric_no,
                    'department' => $user->department,
                    'action_url' => route('admin.users.show', $user->id),
                ]
            ]
        );
    }

    public static function notifyAdminsSystemAlert($title, $message, $type = 'warning', $data = [])
    {
        return self::createForAllAdmins(
            $title,
            $message,
            $type,
            [
                'icon' => 'mdi-alert-circle',
                'data' => $data
            ]
        );
    }

    // ========================================
    // BULK OPERATIONS
    // ========================================

    public static function markAllAsReadForUser($userId)
    {
        return self::where('user_id', $userId)
                   ->where('is_read', false)
                   ->update([
                       'is_read' => true,
                       'read_at' => now(),
                   ]);
    }

    public static function deleteOldNotifications($days = 30)
    {
        return self::where('created_at', '<', now()->subDays($days))
                   ->where('is_read', true)
                   ->delete();
    }

    public static function getUnreadCountForUser($userId)
    {
        return self::where('user_id', $userId)
                   ->where('is_read', false)
                   ->count();
    }

    public static function getRecentForUser($userId, $limit = 10)
    {
        return self::where('user_id', $userId)
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }
}
