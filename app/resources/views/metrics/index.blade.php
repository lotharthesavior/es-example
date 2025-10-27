@use('App\Enums\MetricType')
@use('App\Helpers\Helpers')

<x-layout>
    <div class="container mx-auto p-4">
        <x-header title="{{ __('Health Metrics') }}" />

        @include('parts.breadcrumb')

        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-start gap-6">
                @foreach(MetricType::values() as $value)
                    <a href="{{ route('health.metrics.create', ['metric' => $value]) }}" type="button"
                       class="inline-flex gap-2 items-center rounded-md bg-blue-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-blue-500 dark:hover:bg-blue-400 dark:focus-visible:outline-blue-500 text-nowrap">
                        <!--Plus-->
                        {{--<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>--}}
                        {!! Helpers::metricIcon(MetricType::tryFrom($value), 5) !!}
                        {{ Helpers::prettyString($value) }}
                    </a>
                @endforeach
            </div>

            <div class="mt-8 flow-root">

                <div class="mb-4 flex items-center gap-4">
                    <form method="GET" action="{{ route('health.metrics.index') }}" class="flex items-center gap-2">
                        <label for="type" class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Filter by Type') }}:</label>

                        <el-select name="metric" class="block w-50" value="{{ request('metric') }}">
                            <button type="button" class="grid w-full cursor-default grid-cols-1 rounded-md bg-white py-1.5 pl-3 pr-2 text-left text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-blue-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:focus-visible:outline-blue-500">
                                <el-selectedcontent class="col-start-1 row-start-1 truncate pr-6">{{ __('All') }}</el-selectedcontent>
                                <svg viewBox="0 0 16 16" fill="currentColor" data-slot="icon" aria-hidden="true" class="col-start-1 row-start-1 size-5 self-center justify-self-end text-gray-500 sm:size-4 dark:text-gray-400">
                                    <path d="M5.22 10.22a.75.75 0 0 1 1.06 0L8 11.94l1.72-1.72a.75.75 0 1 1 1.06 1.06l-2.25 2.25a.75.75 0 0 1-1.06 0l-2.25-2.25a.75.75 0 0 1 0-1.06ZM10.78 5.78a.75.75 0 0 1-1.06 0L8 4.06 6.28 5.78a.75.75 0 0 1-1.06-1.06l2.25-2.25a.75.75 0 0 1 1.06 0l2.25 2.25a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                                </svg>
                            </button>

                            <el-options anchor="bottom start" popover class="m-0 max-h-60 w-[var(--button-width)] overflow-auto rounded-md bg-white p-0 py-1 text-base shadow-lg outline outline-1 outline-black/5 [--anchor-gap:theme(spacing.1)] data-[closed]:data-[leave]:opacity-0 data-[leave]:transition data-[leave]:duration-100 data-[leave]:ease-in data-[leave]:[transition-behavior:allow-discrete] sm:text-sm dark:bg-gray-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10">
                                @foreach(MetricType::values() as $value)
                                    <el-option value="{{ $value }}" class="group/option relative cursor-default select-none py-2 pl-3 pr-9 text-gray-900 focus:bg-blue-600 focus:text-white focus:outline-none dark:text-white dark:focus:bg-blue-500 [&:not([hidden])]:block">
                                        <span class="block truncate font-normal group-aria-selected/option:font-semibold">{{ Helpers::prettyString($value) }}</span>
                                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-blue-600 group-focus/option:text-white group-[:not([aria-selected='true'])]/option:hidden dark:text-blue-400 [el-selectedcontent_&]:hidden">
                                            <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5">
                                              <path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </el-option>
                                @endforeach
                            </el-options>
                        </el-select>

                        <button type="submit" class="rounded-md bg-blue-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-blue-500 dark:shadow-none dark:hover:bg-blue-400 dark:focus-visible:outline-blue-500">Filter</button>

                        @if (request()->filled('metric'))
                            <a href="{{ route('health.metrics.index') }}" type="button" class="rounded-md bg-blue-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-blue-500 dark:shadow-none dark:hover:bg-blue-400 dark:focus-visible:outline-blue-500">Reset</a>
                        @endif
                    </form>
                </div>

                <div class="hidden md:block">
                    @include('metrics.desktop.index')
                </div>

                <div class="block md:hidden">
                    @include('metrics.mobile.index')
                </div>
            </div>
        </div>
    </div>
</x-layout>
