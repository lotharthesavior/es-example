<?php

namespace App\Models;

use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent as BaseEloquentStoredEvent;

class StoredEvent extends BaseEloquentStoredEvent
{
    public $casts = [
        'event_properties' => 'array',
        'meta_data' => 'array',
        'created_at' => 'datetime',
    ];
}
