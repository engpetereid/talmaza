<div class="min-h-screen pb-24 bg-gray-50">

    <!-- Header & Search -->
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 mb-6 md:flex-row md:items-center">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-gray-900">لوحة المتابعة ⛪</h1>
                </div>


            </div>

            <!-- Smart Search Bar -->
            <div class="relative">
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                    <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="ابحث عن عائلة أو خادم..."
                    class="w-full py-4 pl-4 pr-12 text-lg font-medium text-gray-900 placeholder-gray-500 transition-all bg-gray-100 border-2 border-transparent shadow-sm focus:bg-white focus:border-indigo-500 rounded-2xl">
            </div>
        </div>
    </div>

    <div class="px-4 py-8 mx-auto space-y-10 max-w-7xl sm:px-6 lg:px-8">

        <!-- System Messages -->
        @if (session()->has('message'))
            <div
                class="flex items-center gap-3 p-4 text-green-800 bg-green-100 border-l-4 border-green-500 shadow-sm rounded-r-xl animate-fade-in-down">
                <div class="p-2 bg-green-200 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12" />
                    </svg></div>
                <span class="text-base font-bold">{{ session('message') }}</span>
            </div>
        @endif



        <!-- 2. Management Tools -->
        <section>
            <h2 class="flex items-center gap-3 mb-6 text-xl font-black text-gray-800">
                <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
                أدوات النظام
            </h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">

                <!-- Reports Tool -->
                <a href="{{ route('admin.reports') }}" wire:navigate
                    class="flex items-center gap-4 p-6 transition-all bg-white border border-gray-100 shadow-sm group rounded-3xl hover:border-indigo-300 hover:shadow-md">
                    <div
                        class="flex items-center justify-center text-indigo-600 transition-colors w-14 h-14 rounded-2xl bg-indigo-50 group-hover:bg-indigo-600 group-hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                            <polyline points="10 9 9 9 8 9" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 transition-colors group-hover:text-indigo-700">متابعة
                            التقارير</h3>
                        <p class="text-sm font-medium text-gray-500">الواردة من القادة</p>
                    </div>
                </a>

                <!-- Announcements Tool -->
                <a href="{{ route('announcements') }}" wire:navigate
                    class="flex items-center gap-4 p-6 transition-all bg-white border border-gray-100 shadow-sm group rounded-3xl hover:border-red-300 hover:shadow-md">
                    <div
                        class="flex items-center justify-center text-red-600 transition-colors w-14 h-14 rounded-2xl bg-red-50 group-hover:bg-red-600 group-hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="17 8 12 3 7 8" />
                            <line x1="12" y1="3" x2="12" y2="15" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 transition-colors group-hover:text-red-700">نشر
                            القرارات</h3>
                        <p class="text-sm font-medium text-gray-500">الإعلانات العامة</p>
                    </div>
                </a>




            </div>
        </section>

        <!-- 3. Families List -->
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="flex items-center gap-3 text-xl font-black text-gray-800">
                    <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
                    متابعة العائلات
                </h2>
                <span
                    class="px-4 py-2 text-sm font-bold text-indigo-800 bg-indigo-100 rounded-xl">{{ count($families) }}
                    عائلة</span>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse($families as $family)
                    <div
                        class="flex flex-col h-full overflow-hidden transition-all duration-300 bg-white border border-gray-200 shadow-sm rounded-3xl hover:border-indigo-400 hover:shadow-lg group">

                        <div class="flex-grow p-6">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex items-center justify-center text-xl font-black text-indigo-700 transition-colors border border-indigo-100 w-14 h-14 rounded-2xl bg-indigo-50 group-hover:bg-indigo-600 group-hover:text-white">
                                        {{ mb_substr($family['name'], 0, 1) }}
                                    </div>
                                    <div>
                                        <h3 class="mb-1 text-lg font-bold leading-tight text-gray-900">{{ $family['name'] }}
                                        </h3>
                                        <p class="flex items-center gap-1 text-sm font-medium text-gray-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
                                            {{ $family['leader_name'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <hr class="mb-4 border-gray-100">

                            <!-- Details Grid -->
                            <div class="grid grid-cols-1 text-sm gap-y-3">
                                <div>
                                    <span class="block mb-2 text-xs font-bold text-gray-400">الدرس الحالي</span>
                                    <span
                                        class="inline-block px-3 py-1 text-xs font-bold text-indigo-700 border border-indigo-100 rounded-lg bg-indigo-50">
                                        {{ $family['stage_name'] . $family['last_lesson'] ? '- ' . $family['last_lesson'] : '' }}
                                    </span>
                                </div>

                                <div class="col-span-2">
                                    <div class="inline-block" style="margin-left: 50px">
                                        <span class="block mb-1 text-xs font-bold text-gray-400 ">عدد
                                            المخدومين</span>
                                        <span class="font-bold text-gray-800">{{ $family['members_count'] }} مخدوم</span>
                                    </div>
                                    <div class="inline-block mt-2">
                                        <span class="block mb-1 text-xs font-bold text-gray-400">آخر اجتماع</span>
                                        <span class="flex items-center gap-1 font-bold text-gray-800">
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="10" />
                                                <polyline points="12 6 12 12 16 14" />
                                            </svg>
                                            {{ $family['last_meeting_date'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer / Action -->
                        <a href="{{ route('admin.family.view', $family['id']) }}" wire:navigate
                            class="flex items-center justify-center gap-2 p-4 font-bold text-center text-indigo-600 transition-colors border-t border-gray-100 bg-gray-50 hover:bg-indigo-600 hover:text-white group-hover:underline">
                            <span>عرض التفاصيل والإحصائيات</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </a>
                    </div>
                @empty
                    <div
                        class="py-16 text-center bg-white border-2 border-gray-200 border-dashed col-span-full rounded-3xl">
                        <div
                            class="flex items-center justify-center w-20 h-20 mx-auto mb-4 text-4xl text-gray-300 rounded-full bg-gray-50">
                            🔍</div>
                        <p class="text-lg font-bold text-gray-500">لا توجد عائلات مطابقة للبحث</p>
                        <p class="mt-1 text-sm text-gray-400">جرب البحث باسم خادم أو اسم عائلة مختلف</p>
                    </div>
                @endforelse
            </div>
        </section>
        <!-- 1. Key Metrics Cards -->
        <section>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 md:gap-6">
                <!-- Families Card -->
                <div
                    class="flex items-center gap-5 p-6 transition-shadow bg-white border border-gray-100 shadow-sm rounded-3xl hover:shadow-md">
                    <div class="flex items-center justify-center w-16 h-16 text-indigo-600 bg-indigo-100 rounded-2xl">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path
                                d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z" />
                            <path
                                d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z" />
                        </svg>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-bold tracking-wider text-gray-500 uppercase">إجمالي العائلات</p>
                        <h3 class="text-4xl font-black text-gray-900">{{ $stats['families_count'] }}</h3>
                    </div>
                </div>

                <!-- Members Card -->
                <div
                    class="flex items-center gap-5 p-6 transition-shadow bg-white border border-gray-100 shadow-sm rounded-3xl hover:shadow-md">
                    <div class="flex items-center justify-center w-16 h-16 text-blue-600 bg-blue-100 rounded-2xl">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-bold tracking-wider text-gray-500 uppercase">المخدومين النشطين</p>
                        <h3 class="text-4xl font-black text-gray-900">{{ $stats['members_count'] }}</h3>
                    </div>
                </div>

                <!-- Activity Card -->
                <div
                    class="flex items-center gap-5 p-6 transition-shadow bg-white border border-gray-100 shadow-sm rounded-3xl hover:shadow-md">
                    <div class="flex items-center justify-center w-16 h-16 text-green-600 bg-green-100 rounded-2xl">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-bold tracking-wider text-gray-500 uppercase">اجتماعات الأسبوع</p>
                        <h3 class="text-4xl font-black text-gray-900">{{ $stats['active_meetings_this_week'] }}</h3>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>
