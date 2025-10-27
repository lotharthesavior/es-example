@use('App\Enums\MetricType')
@use('App\Helpers\Helpers')

<ul role="list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($metrics as $metric)
        <li class="col-span-1 divide-y divide-gray-200 rounded-lg bg-white shadow dark:divide-white/10 dark:bg-gray-800/50 dark:shadow-none dark:outline dark:outline-1 dark:-outline-offset-1 dark:outline-white/10">
            <div class="flex w-full items-center justify-between space-x-6 p-6">
                <div class="flex-1 truncate">
                    <div class="flex items-center space-x-3">
                        <h3 class="truncate text-sm font-medium text-gray-900 dark:text-white">
                            {{ __(ucwords(str_replace('_', ' ', $metric->type->value))) }}
                        </h3>
                        <span class="inline-flex shrink-0 items-center rounded-full bg-green-50 px-1.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-500 dark:ring-green-500/10">
                            {{ $metric->timestamp->format('Y-m-d H:i') }}
                        </span>
                    </div>
                    <p class="mt-1 truncate text-sm text-gray-500 dark:text-gray-400">
                        {{ Helpers::presentMetric($metric) }}
                    </p>
                </div>
                {!! Helpers::metricIcon($metric->type) !!}
            </div>
            <div>
                <div class="-mt-px flex divide-x divide-gray-200 dark:divide-white/10">
                    <div class="flex w-0 flex-1">
                        <a href="{{ route('health.metrics.edit', $metric) }}" class="relative -mr-px inline-flex w-0 flex-1 items-center justify-center gap-x-3 rounded-bl-lg border border-transparent py-4 text-sm font-semibold text-gray-900 dark:text-white">
                            <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 text-gray-400 dark:text-gray-500">
                                <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
                            </svg>
                            {{ __('Edit') }}
                        </a>
                    </div>
                    <div class="-ml-px flex w-0 flex-1">
                        <form action="{{ route('health.metrics.destroy', $metric) }}" method="POST" class="relative inline-flex w-0 flex-1 items-center justify-center gap-x-3 rounded-br-lg border border-transparent py-4 text-sm font-semibold text-gray-900 dark:text-white">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center gap-x-3 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('{{ __('Are you sure?') }}')">
                                <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5">
                                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                </svg>
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </li>
    @empty
        <li class="col-span-1 divide-y divide-gray-200 rounded-lg bg-white shadow dark:divide-white/10 dark:bg-gray-800/50 dark:shadow-none dark:outline dark:outline-1 dark:-outline-offset-1 dark:outline-white/10">
            <div class="flex w-full items-center justify-between space-x-6 p-6">
                <div class="flex-1 truncate">
                    <p class="mt-1 truncate text-sm text-gray-500 dark:text-gray-400">
                        {{ __('No metrics recorded.') }}
                    </p>
                </div>
            </div>
        </li>
    @endforelse
</ul>
<div class="mt-6">
    {{ $metrics->links() }}
</div>
