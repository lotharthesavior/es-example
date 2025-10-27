<?php

namespace App\Http\Middleware;

use App\Domains\AppInstance\Aggregates\AppInstanceAggregate;
use App\Domains\AppInstance\Projections\AppInstance;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ProfileMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $appInstance = AppInstance::first();

        if ($appInstance === null) {
            $instanceUuid = Str::uuid()->toString();

            AppInstanceAggregate::retrieve($instanceUuid)
                ->initializeApp()
                ->persist();

            Context::add('instance', $instanceUuid);

            return $next($request);
        }

        Context::add('profile', $appInstance->profile_uuid);
        Context::add('instance', $appInstance->uuid);

        return $next($request);
    }
}
