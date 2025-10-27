<div>
    <header class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">{{ $title }}</h1>
        <div class="flex items-center space-x-4">
            <!-- Profile Selector -->
            <form id="profile-form" action="{{ route('set.profile') }}" method="POST">
                {{--<input type="hidden" name="profile_uuid" value="{{ url()->current() }}">--}}
                @csrf
                <label for="profile_uuid" class="sr-only">{{ __('Select Profile') }}</label>
                @if(count($profiles) > 0)
                    <div class="grid grid-cols-1">
                        <el-select name="profile_uuid" value="{{ Context::get('profile') }}">
                            <div class="inline-flex divide-x divide-blue-700 rounded-md outline-none dark:divide-blue-600 w-full">
                                <div class="inline-flex items-center gap-x-1.5 rounded-l-md bg-blue-600 px-3 py-2 text-white dark:bg-blue-500 w-full">
                                    {{--Person Icon--}}
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <el-selectedcontent class="text-sm font-semibold">
                                        @php
                                            $selectedProfile = $profiles->firstWhere('uuid', Context::get('profile'));
                                        @endphp
                                        {{ $selectedProfile ? $selectedProfile->name . ' (' . __($selectedProfile->role) . ')' : __('Select a Profile') }}
                                    </el-selectedcontent>
                                </div>
                                <button type="button" aria-label="{{ __('Change selected profile') }}" class="inline-flex items-center rounded-l-none rounded-r-md bg-blue-600 p-2 hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-400 dark:bg-blue-500 dark:hover:bg-blue-400 dark:focus-visible:outline-blue-400">
                                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 text-white forced-colors:text-[Highlight]">
                                        <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <el-options anchor="bottom end" popover class="m-0 w-72 origin-top-right divide-y divide-gray-200 overflow-hidden rounded-md bg-white p-0 shadow-lg outline outline-1 outline-black/5 [--anchor-gap:theme(spacing.2)] data-[closed]:data-[leave]:opacity-0 data-[leave]:transition data-[leave]:duration-100 data-[leave]:ease-in data-[leave]:[transition-behavior:allow-discrete] dark:divide-white/10 dark:bg-gray-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10">
                                @foreach($profiles as $profile)
                                    <el-option value="{{ $profile->uuid }}" class="group/option cursor-default select-none p-4 text-sm text-gray-900 focus:bg-blue-600 focus:text-white focus:outline-none dark:text-white dark:focus:bg-blue-500 [&:not([hidden])]:block">
                                        <div class="flex flex-col">
                                            <div class="flex justify-between">
                                                <p class="font-normal group-aria-selected/option:font-semibold [el-selectedcontent_&]:font-semibold">
                                                    {{ $profile->name }}
                                                </p>
                                                <span class="text-blue-600 group-focus/option:text-white group-[:not([aria-selected='true'])]/option:hidden dark:text-blue-400 [el-selectedcontent_&]:hidden">
                                              <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5">
                                                <path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" />
                                              </svg>
                                            </span>
                                            </div>
                                            <p class="mt-2 text-gray-500 group-focus/option:text-blue-200 dark:text-gray-400 dark:group-focus/option:text-blue-100 [el-selectedcontent_&]:hidden">
                                                {{ __('Switch to this profile.') }}
                                            </p>
                                        </div>
                                    </el-option>
                                @endforeach
                            </el-options>
                        </el-select>
                    </div>
                @endif
            </form>

            <a href="{{ route('profiles.index') }}" type="button" class="inline-flex items-center gap-x-2 rounded-md bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-blue-500 dark:shadow-none dark:hover:bg-blue-400 dark:focus-visible:outline-blue-500">
                {{--Person Plus--}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
            </a>

            <!-- Logout -->
            {{--<form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-500">{{ __('Logout') }}</button>
            </form>--}}
        </div>
    </header>

    @if (session('success'))
        <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <script>
        function startElements() {
            document.getElementsByName('profile_uuid')[0]
                .addEventListener('change', () => document.getElementById('profile-form').submit());
        }

        if (customElements.get('el-select')) {
            startElements()
        } else {
            window.addEventListener('elements:ready', startElements);
        }
    </script>
</div>
