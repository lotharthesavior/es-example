<?php

namespace App\Domains\HealthProfile\Projections;

use App\Domains\Profile\Projections\Profile;
use App\Enums\MetricType;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EventSourcing\Projections\Projection;

class Metric extends Projection
{
    use HasUuid;

    protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'profile_uuid',
        'type',
        'value',
        'notes',
        'photo_url',
        'timestamp',
    ];

    protected $casts = [
        'type' => MetricType::class,
        'value' => 'array',
        'timestamp' => 'datetime',
    ];

    /**
     * @return BelongsTo<Profile>
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
