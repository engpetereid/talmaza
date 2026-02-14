<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <!-- Prevent zooming issues on mobile inputs -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'خدمة التلمذة') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cairo:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* Hide Scrollbar but keep functionality */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Better focus for accessibility */
        :focus-visible {
            outline: 2px solid #4f46e5;
            outline-offset: 2px;
        }
    </style>
</head>

<body
    class="pb-24 font-sans antialiased text-gray-900 bg-gray-50 md:pb-0 selection:bg-indigo-100 selection:text-indigo-700">

    <div class="flex flex-col min-h-screen">

        <!-- Top Header (Desktop & Mobile) -->
        <header class="sticky top-0 z-40 bg-white border-b border-gray-100 shadow-sm">
            <div class="flex items-center justify-between h-20 px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

                <!-- Logo / Title -->
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/Artboard.jpg') }}" alt="Logo" class="object-contain"
                        style="max-height: 110px">
                </div>

                <!-- DESKTOP NAVIGATION (New Feature) -->
                <!-- Visible only on md screens and up -->
                <nav class="hidden md:flex items-center gap-1 bg-gray-50 p-1.5 rounded-2xl border border-gray-200">

                    @php
                        $navLinks = [
                            ['route' => 'dashboard', 'label' => 'الرئيسية', 'icon' => 'home'],
                            ['route' => 'announcements', 'label' => 'القرارات', 'icon' => 'bell'],
                            ['route' => 'lessons.library', 'label' => 'المناهج', 'icon' => 'book'],
                        ];

                        // Admin Link Addition
                        if (Auth::check() && Auth::user()->role == 'admin') {
                            $navLinks[] = ['route' => 'admin.add-family', 'label' => 'اضافه عائلة', 'icon' => 'clipboard'];
                        } else {
                            // Leader Reports
                            $navLinks[] = ['route' => 'leader.reports', 'label' => 'التقارير', 'icon' => 'clipboard'];
                        }
                        if (Auth::check() && Auth::user()->role == 'admin') {
                        } else {
                            // Leader Reports
                            $navLinks[] =['route' => 'my-family', 'label' => 'عائلتي', 'icon' => 'users'];
                        }
                    @endphp

                    @foreach($navLinks as $link)
                        <a href="{{ route($link['route']) }}" wire:navigate
                            class="px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2
                                                               {{ request()->routeIs($link['route']) ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>

                <!-- User Actions (Notifications & Profile) -->
                <div class="flex items-center gap-3">
                    <!-- Notifications -->
                    <a href="{{ route('notifications') }}" wire:navigate
                        class="relative flex items-center justify-center w-12 h-12 text-gray-600 transition-colors rounded-2xl hover:bg-gray-100 group"
                        aria-label="الإشعارات">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="transition-colors group-hover:text-indigo-600">
                            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
                        </svg>

                        @if(auth()->user() && auth()->user()->unreadNotifications->count() > 0)
                            <span
                                class="absolute top-2 right-2 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] text-white font-bold border-2 border-white animate-bounce shadow-sm">
                                {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </a>

                    <!-- Profile -->
                    <a href="{{ route('profile') }}" wire:navigate
                        class="flex items-center justify-center w-12 h-12 text-lg font-bold text-indigo-700 transition-all border-2 border-transparent shadow-sm rounded-2xl bg-indigo-50 hover:bg-indigo-100 hover:scale-105 hover:border-indigo-200"
                        aria-label="الملف الشخصي">
                        ⚙︎
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow">
            {{ $slot }}
        </main>

        <!-- Footer (Desktop Only) -->
        <footer class="hidden py-8 mt-auto bg-white border-t border-gray-200 md:block">
            <div class="px-4 mx-auto text-sm text-center text-gray-400 max-w-7xl">
                <p>&copy; {{ date('Y') }} خدمة التلمذة. جميع الحقوق محفوظة.</p>
            </div>
        </footer>

    </div>

    <!-- Bottom Navigation Bar (Mobile Only) -->
    <div x-data="{ openActionMenu: false }" class="fixed bottom-0 left-0 z-50 w-full md:hidden pb-safe">

        <!-- Floating Action Menu (Popup) -->
        <div x-show="openActionMenu" x-cloak @click.away="openActionMenu = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-10 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-10 scale-95"
            class="absolute left-0 w-full px-4 mb-2 bottom-24">

            <div
                class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.2)] border border-white/50 p-3 space-y-2 ring-1 ring-gray-100">
                @if(Auth::check() && Auth::user()->role == 'admin')
                    <a href="{{ route('admin.add-family') }}" wire:navigate @click="openActionMenu = false"
                        class="flex items-center gap-4 p-4 transition-colors rounded-2xl hover:bg-indigo-50 active:scale-95 group">
                        <div
                            class="flex items-center justify-center w-12 h-12 text-indigo-600 transition-colors bg-indigo-100 rounded-full group-hover:bg-indigo-600 group-hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="8.5" cy="7" r="4" />
                                <line x1="20" y1="8" x2="20" y2="14" />
                                <line x1="23" y1="11" x2="17" y2="11" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-base font-black text-gray-800">إضافة عائلة</span>
                            <span class="text-xs font-bold text-gray-500">تسجيل خادم وعائلة جديدة</span>
                        </div>
                    </a>
                @else
                    <!-- Leader Actions -->
                    <a href="{{ route('dashboard') }}" wire:navigate @click="openActionMenu = false"
                        class="flex items-center gap-4 p-4 transition-colors rounded-2xl hover:bg-green-50 active:scale-95 group">
                        <div
                            class="flex items-center justify-center w-12 h-12 text-green-600 transition-colors bg-green-100 rounded-full group-hover:bg-green-600 group-hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9" />
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-base font-black text-gray-800">تسجيل الاجتماع</span>
                            <span class="text-xs font-bold text-gray-500">الذهاب للوحة القيادة</span>
                        </div>
                    </a>
                @endif

                <a href="{{ route('announcements') }}" wire:navigate @click="openActionMenu = false"
                    class="flex items-center gap-4 p-4 transition-colors rounded-2xl hover:bg-red-50 active:scale-95 group">
                    <div
                        class="flex items-center justify-center w-12 h-12 text-red-600 transition-colors bg-red-100 rounded-full group-hover:bg-red-600 group-hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="17 8 12 3 7 8" />
                            <line x1="12" y1="3" x2="12" y2="15" />
                        </svg>
                    </div>
                    <div>
                        <span
                            class="block text-base font-black text-gray-800">{{ Auth::user()->role == 'admin' ? 'نشر قرار' : 'القرارات' }}</span>
                        <span class="text-xs font-bold text-gray-500">لوحة الإعلانات العامة</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Bottom Navbar -->
        <div
            class="bg-white/95 backdrop-blur-md border-t border-gray-200 flex justify-around items-end pb-3 pt-2 shadow-[0_-4px_20px_rgba(0,0,0,0.05)] h-20">

            <!-- Home -->
            <a href="{{ Auth::check() && Auth::user()->role == 'admin' ? route('admin.dashboard') : route('dashboard') }}"
                wire:navigate
                class="flex flex-col items-center gap-1 w-16 transition-all duration-300 {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'text-indigo-600 -translate-y-1' : 'text-gray-400 hover:text-gray-600' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="{{ request()->routeIs('dashboard') ? '3' : '2' }}"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                <span class="text-[10px] font-bold">الرئيسية</span>
            </a>

            <!-- Lessons -->
            <a href="{{ route('lessons.library') }}" wire:navigate
                class="flex flex-col items-center gap-1 w-16 transition-all duration-300 {{ request()->routeIs('lessons.library') ? 'text-indigo-600 -translate-y-1' : 'text-gray-400 hover:text-gray-600' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="{{ request()->routeIs('lessons.library') ? '3' : '2' }}"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20" />
                </svg>
                <span class="text-[10px] font-bold">المناهج</span>
            </a>

            <!-- Floating Action Button -->
            <div class="relative -top-6">
                <button @click="openActionMenu = !openActionMenu"
                    class="flex items-center justify-center w-16 h-16 text-white transition-all bg-indigo-600 rounded-full shadow-xl shadow-indigo-300 hover:bg-indigo-700 hover:scale-105 active:scale-95 ring-4 ring-gray-50">
                    <svg x-show="!openActionMenu" xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round" class="transition-transform duration-300">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    <svg x-show="openActionMenu" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="32"
                        height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round"
                        class="transition-transform duration-300 rotate-90">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            <!-- Reports (Leader) or Admin Tools -->
            @if(Auth::check() && Auth::user()->role == 'admin')
                <a href="{{ route('admin.reports') }}" wire:navigate
                    class="flex flex-col items-center gap-1 w-16 transition-all duration-300 {{ request()->routeIs('admin.reports') ? 'text-indigo-600 -translate-y-1' : 'text-gray-400 hover:text-gray-600' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="{{ request()->routeIs('admin.reports') ? '3' : '2' }}"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                        <polyline points="10 9 9 9 8 9" />
                    </svg>
                    <span class="text-[10px] font-bold">التقارير</span>
                </a>
            @else
                <a href="{{ route('leader.reports') }}" wire:navigate
                    class="flex flex-col items-center gap-1 w-16 transition-all duration-300 {{ request()->routeIs('leader.reports') ? 'text-indigo-600 -translate-y-1' : 'text-gray-400 hover:text-gray-600' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="{{ request()->routeIs('leader.reports') ? '3' : '2' }}"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                        <polyline points="10 9 9 9 8 9" />
                    </svg>
                    <span class="text-[10px] font-bold">التقارير</span>
                </a>
            @endif

            <!-- Family -->
            <a href="{{ route('my-family') }}" wire:navigate
                class="flex flex-col items-center gap-1 w-16 transition-all duration-300 {{ request()->routeIs('my-family') ? 'text-indigo-600 -translate-y-1' : 'text-gray-400 hover:text-gray-600' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="{{ request()->routeIs('my-family') ? '3' : '2' }}"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                <span class="text-[10px] font-bold">عائلتي</span>
            </a>

        </div>
    </div>
</body>

</html>
