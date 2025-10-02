<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class IdCardRequest extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'request_number',
        'photo',
        'reason',
        'status',
        'admin_feedback',
        'id_card_file',
        'reviewed_by',
        'reviewed_at',
        'printed_at',
        'collected_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'printed_at' => 'datetime',
        'collected_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid();
            }

            if (empty($model->request_number)) {
                $model->request_number = self::generateRequestNumber();
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Helper methods
    public static function generateRequestNumber()
    {
        $year = date('Y');
        $lastRequest = self::whereYear('created_at', $year)
                          ->orderBy('created_at', 'desc')
                          ->first();

        $number = $lastRequest ?
                 intval(substr($lastRequest->request_number, -3)) + 1 : 1;

        return 'IDR-' . $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    public function getIdCardUrlAttribute()
    {
        return $this->id_card_file ? asset('storage/' . $this->id_card_file) : null;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-warning',
            'approved' => 'bg-info',
            'rejected' => 'bg-danger',
            'printed' => 'bg-primary',
            'ready' => 'bg-success',
            'collected' => 'bg-secondary',
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    public function canBeDownloaded()
    {
        return in_array($this->status, ['ready', 'collected']) && $this->id_card_file;
    }
}
