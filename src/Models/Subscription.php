<?php

namespace Aldeebhasan\LaSubscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Subscription extends Model
{
    protected $fillable = ['owner_type', 'owner_id', 'start_at', 'end_at', 'trial_end_at', 'billing_period'];
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'trial_end_at' => 'datetime',
    ];

    public function getTable(): string
    {
        $prefix = config('subscription.prefix');
        $table = parent::getTable();

        return "{$prefix}_{$table}";
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
