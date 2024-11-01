<?php

namespace Aldeebhasan\LaSubscription\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'type'];

    public function getTable(): string
    {
        $prefix = config('subscription.prefix');
        $table = parent::getTable();

        return "{$prefix}_{$table}";
    }
}
