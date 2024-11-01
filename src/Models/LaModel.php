<?php

namespace Aldeebhasan\LaSubscription\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaModel extends Model
{
    use HasFactory;

    public function getTable(): string
    {
        $prefix = config('subscription.prefix');
        $table = parent::getTable();

        return "{$prefix}_{$table}";
    }
}
