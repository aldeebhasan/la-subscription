<?php

namespace Aldeebhasan\LaSubscription\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LaModel extends Model
{
    use HasFactory, SoftDeletes;

    public function getTable(): string
    {
        $prefix = config('subscription.prefix');
        $table = Str::snake(Str::pluralStudly(class_basename($this)));

        return "{$prefix}_{$table}";
    }
}
