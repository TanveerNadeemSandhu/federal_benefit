<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Share extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'share_id',
        'share_role',
        'status',
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'share_id', 'id');
    }
    public function shareUsers(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
