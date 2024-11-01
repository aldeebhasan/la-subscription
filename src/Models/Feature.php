<?php

namespace Aldeebhasan\LaSubscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feature extends Model
{
    protected $fillable = ['name', 'code', 'description', 'group_id', 'active', 'limited'];
    protected $casts = [
        'limited' => 'bool',
        'active' => 'bool',
    ];

    public function getTable(): string
    {
        $prefix = config('subscription.prefix');
        $table = parent::getTable();

        return "{$prefix}_{$table}";
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class)->withDefault(function (Group $group) {
            $group->setAttribute('name', 'Others');
        });
    }
}
