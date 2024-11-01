<?php

namespace Aldeebhasan\LaSubscription\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property Collection<SubscriptionContract> $contracts
 * @property string $start_at
 * @property string $end_at
 */
class Subscription extends LaModel
{
    protected $fillable = ['owner_type', 'owner_id', 'start_at', 'end_at', 'billing_period'];
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'trial_end_at' => 'datetime',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(SubscriptionContract::class);
    }
}
