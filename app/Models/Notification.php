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
}
