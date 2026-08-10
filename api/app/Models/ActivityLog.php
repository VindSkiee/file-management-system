<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_name',
    ];

    /**
     * Get the user who performed the action.
     *
     * @return BelongsTo<User, ActivityLog>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
