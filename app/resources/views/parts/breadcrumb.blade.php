<nav aria-label="Breadcrumb" class="flex mb-6">
    <ol role="list" class="flex space-x-4 rounded-md bg-white px-6 shadow dark:bg-gray-800/50 dark:shadow-none dark:outline dark:outline-1 dark:-outline-offset-1 dark:outline-white/10">
        <li class="flex">
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="text-gray-400 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 shrink-0">
                        <path d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" fill-rule="evenodd" />
                    </svg>
                    <span class="sr-only">Home</span>
                </a>
            </div>
        </li>
        @php
            // TODO: this can be better in dynamically adding breadcrumb
            $cleanUri = preg_replace('/\{[^}]+\}/', '', Request::route()->uri);
            $segments = array_filter(explode('/', $cleanUri));
            $url = '';
        @endphp
        @foreach($segments as $index => $segment)
            <li class="flex">
                <div class="flex items-center">
                    <svg viewBox="0 0 24 44" fill="currentColor" preserveAspectRatio="none" aria-hidden="true" class="h-full w-6 shrink-0 text-gray-200 dark:text-white/10">
                        <path d="M.293 0l22 22-22 22h1.414l22-22-22-22H.293z" />
                    </svg>
                    @php $url .= '/' . $segment; @endphp
                    @if($index + 1 < count($segments))
                        <a href="{{ url($url) }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            {{ ucfirst(str_replace('-', ' ', $segment)) }}
                        </a>
                    @else
                        <a href="#" aria-current="page" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            {{ ucfirst(str_replace('-', ' ', $segment)) }}
                        </a>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>
