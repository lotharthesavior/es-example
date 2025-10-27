<?php

use App\Domains\HealthProfile\Queries\TimeMachineMetricsQuery;

// case 1
$result = (new TimeMachineMetricsQuery())->until('2025-10-24T19:53:35+00:00')->get();

// case 2
// $result = (new TimeMachineMetricsQuery())->until('2025-10-24T19:52:35+00:00')->get();

dump($result->toArray());
