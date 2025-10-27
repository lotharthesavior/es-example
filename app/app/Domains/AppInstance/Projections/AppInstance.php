<?php

namespace App\Domains\AppInstance\Projections;

use App\Models\Traits\HasUuid;
use Spatie\EventSourcing\Projections\Projection;

class AppInstance extends Projection
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'profile_uuid',
    ];

    protected $guarded = [];

    protected $primaryKey = 'uuid';

    protected $keyType = 'string';
}
