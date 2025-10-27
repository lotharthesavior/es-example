@use('App\Enums\MetricType')
@use('App\Helpers\Helpers')

<div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
        <table class="relative min-w-full divide-y divide-gray-300 dark:divide-white/15">
            <thead>
                <tr>
                    <th scope="col"
                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0 dark:text-white">
                        {{ __('Date') }}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                        {{ __('Type') }}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                        {{ __('Value') }}</th>
                    <th scope="col" class="py-3.5 pl-3 pr-4 sm:pr-0">
                        <span class="sr-only">{{ __('Actions') }}</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900">
                @forelse($metrics as $metric)
                    <tr>
                        <td class="whitespace-nowrap py-5 pl-4 pr-3 text-sm sm:pl-0">
                            <div class="text-gray-900 dark:text-white">{{ $metric->timestamp->format('Y-m-d H:i') }}
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500 dark:text-gray-400">
                            <div class="text-gray-900 dark:text-white">
                                {{ __(ucwords(str_replace('_', ' ', $metric->type->value))) }}</div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500 dark:text-gray-400">
                            {{ Helpers::presentMetric($metric) }}
                        </td>
                        <td class="whitespace-nowrap py-5 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                            <a href="{{ route('health.metrics.edit', $metric) }}"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">{{ __('Edit') }}<span
                                    class="sr-only">,
                                    {{ __(ucwords(str_replace('_', ' ', $metric->type->value))) }}</span></a>
                            <form action="{{ route('health.metrics.destroy', $metric) }}" method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 ml-2"
                                    onclick="return confirm('{{ __('Are you sure?') }}')">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4"
                            class="whitespace-nowrap py-5 pl-4 pr-3 text-sm text-center text-gray-500 dark:text-gray-400 sm:pl-0">
                            {{ __('No metrics recorded.') }}
                        </td>
                    </tr>
                @endforelse
                <tr>
                    <td colspan="4" class="pt-6">
                        {{ $metrics->links() }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
