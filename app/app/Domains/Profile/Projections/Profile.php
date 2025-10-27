<?php

namespace App\Domains\Profile\Projections;

use App\Domains\HealthProfile\Projections\Metric;
use App\Models\Traits\HasUuid;
use App\Models\User;
use Spatie\EventSourcing\Projections\Projection;

class Profile extends Projection
{
    use HasUuid;

    protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'user_id',
        'role',
        'name',
        'instance_uuid',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'role' => 'string', // e.g., 'patient', 'caregiver'
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function metrics()
    {
        return $this->hasMany(Metric::class);
    }
}
