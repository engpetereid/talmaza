<div class="min-h-screen pb-24 bg-gray-50">

    <!-- Header -->
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                <div class="flex items-center gap-4">
                    <!-- Profile Avatar -->
                    <div
                        class="flex items-center justify-center text-2xl font-black text-indigo-700 bg-indigo-100 w-14 h-14 rounded-2xl">
                        {{ mb_substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-black leading-tight text-gray-900">أهلاً، {{ Auth::user()->name }} 👋
                        </h1>
                        <p class="mt-1 text-base font-bold text-gray-600">خادم {{ $family->name ?? '...' }}</p>
                    </div>
                </div>

                <div class="hidden text-sm font-medium text-gray-400 md:block">
                    {{ \Carbon\Carbon::now()->locale('ar')->isoFormat('dddd D MMMM YYYY') }}
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 py-8 mx-auto space-y-10 max-w-7xl sm:px-6 lg:px-8">

        <!-- 1. Current Week Card (Hero) -->
        <section>
            <div class="relative overflow-hidden bg-white border border-indigo-100 shadow-lg rounded-3xl">
                <!-- Status Strip -->
                <div
                    class="absolute top-0 right-0 bottom-0 w-3 {{ $currentMeeting && $currentMeeting->status == 'completed' ? 'bg-green-500' : 'bg-indigo-500' }}">
                </div>

                <div class="flex flex-col items-start justify-between gap-6 p-6 md:p-10 md:flex-row md:items-center">
                    <div class="flex-grow">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 text-sm font-bold text-indigo-700 bg-indigo-100 rounded-lg">هذا
                                الأسبوع</span>
                            <span
                                class="text-sm font-bold text-gray-400">{{ \Carbon\Carbon::now()->locale('ar')->isoFormat('D MMMM') }}</span>
                        </div>
                        <h2 class="mb-2 text-3xl font-black text-gray-900 md:text-4xl">اجتماع التلمذة</h2>
                        @if($currentMeeting && $currentMeeting->status == 'pending')
                            <p class="text-lg font-medium text-gray-600">لم يتم تسجيل بيانات التتميم والحضور بعد.</p>
                        @elseif($currentMeeting)
                            <p class="flex items-center gap-2 text-lg font-bold text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                                تم تسجيل الاجتماع بنجاح
                            </p>
                        @else
                            <p class="text-lg font-medium text-gray-500">لا يوجد اجتماع نشط حالياً.</p>
                        @endif
                    </div>

                    <div class="flex-shrink-0 w-full md:w-auto">
                        @if($currentMeeting)
                            @if($currentMeeting->status == 'pending')
                                <a href="{{ route('meeting.record', $currentMeeting->id) }}" wire:navigate
                                    class="flex items-center justify-center w-full gap-3 px-8 py-4 text-lg font-black text-white transition-transform bg-indigo-600 shadow-xl md:w-auto hover:bg-indigo-700 rounded-2xl shadow-indigo-200 hover:-translate-y-1 active:scale-95">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 20h9" />
                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
                                    </svg>
                                    تسجيل التتميم
                                </a>
                            @else
                                <a href="{{ route('meeting.record', $currentMeeting->id) }}" wire:navigate
                                    class="flex items-center justify-center w-full gap-3 px-8 py-4 text-lg font-bold text-gray-700 transition-all bg-white border-2 border-gray-200 md:w-auto hover:border-indigo-300 hover:text-indigo-700 rounded-2xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    مراجعة البيانات
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <div class="grid items-start grid-cols-1 gap-8 lg:grid-cols-3">

            <!-- 2. Quick Access Grid -->
            <div class="space-y-6 lg:col-span-2">
                <h3 class="flex items-center gap-2 text-xl font-black text-gray-800">
                    <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
                    الوصول السريع
                </h3>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                    <!-- Family Data -->
                    <a href="{{ route('my-family') }}" wire:navigate
                        class="flex flex-col items-center justify-center gap-4 p-6 text-center transition-all bg-white border border-gray-100 shadow-sm group rounded-3xl hover:border-blue-300 hover:shadow-md">
                        <div
                            class="flex items-center justify-center w-16 h-16 text-blue-600 transition-colors rounded-2xl bg-blue-50 group-hover:bg-blue-600 group-hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-800 group-hover:text-blue-700">بيانات العيلة</span>
                    </a>

                    <!-- Reports -->
                    <a href="{{ route('leader.reports') }}" wire:navigate
                        class="flex flex-col items-center justify-center gap-4 p-6 text-center transition-all bg-white border border-gray-100 shadow-sm group rounded-3xl hover:border-indigo-300 hover:shadow-md">
                        <div
                            class="flex items-center justify-center w-16 h-16 text-indigo-600 transition-colors rounded-2xl bg-indigo-50 group-hover:bg-indigo-600 group-hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                                <line x1="16" y1="13" x2="8" y2="13" />
                                <line x1="16" y1="17" x2="8" y2="17" />
                                <polyline points="10 9 9 9 8 9" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-800 group-hover:text-indigo-700">التقارير</span>
                    </a>

                    <!-- Announcements -->
                    <a href="{{ route('announcements') }}" wire:navigate
                        class="flex flex-col items-center justify-center gap-4 p-6 text-center transition-all bg-white border border-gray-100 shadow-sm group rounded-3xl hover:border-red-300 hover:shadow-md">
                        <div
                            class="flex items-center justify-center w-16 h-16 text-red-600 transition-colors rounded-2xl bg-red-50 group-hover:bg-red-600 group-hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <polyline points="17 8 12 3 7 8" />
                                <line x1="12" y1="3" x2="12" y2="15" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-800 group-hover:text-red-700">القرارات</span>
                    </a>

                    <!-- Stats (Monthly) -->
                    <a href="{{ route('stats') }}" wire:navigate
                        class="flex flex-col items-center justify-center gap-4 p-6 text-center transition-all bg-white border border-gray-100 shadow-sm group rounded-3xl hover:border-purple-300 hover:shadow-md">
                        <div
                            class="flex items-center justify-center w-16 h-16 text-purple-600 transition-colors rounded-2xl bg-purple-50 group-hover:bg-purple-600 group-hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="20" x2="18" y2="10" />
                                <line x1="12" y1="20" x2="12" y2="4" />
                                <line x1="6" y1="20" x2="6" y2="14" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-800 group-hover:text-purple-700">نسب الشهر</span>
                    </a>

                    <!-- Stats (Stage) -->
                    <a href="{{ route('stage.stats') }}" wire:navigate
                        class="flex flex-col items-center justify-center gap-4 p-6 text-center transition-all bg-white border border-gray-100 shadow-sm group rounded-3xl hover:border-teal-300 hover:shadow-md">
                        <div
                            class="flex items-center justify-center w-16 h-16 text-teal-600 transition-colors rounded-2xl bg-teal-50 group-hover:bg-teal-600 group-hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 10v6M2 10v6" />
                                <path
                                    d="M20 21a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3zm2-5a2 2 0 0 0-2-2h-2V8a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v4H4a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2z" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-800 group-hover:text-teal-700">نسب المرحلة</span>
                    </a>

                    <!-- Lessons -->
                    <a href="{{ route('lessons.library') }}" wire:navigate
                        class="flex flex-col items-center justify-center gap-4 p-6 text-center transition-all bg-white border border-gray-100 shadow-sm group rounded-3xl hover:border-orange-300 hover:shadow-md">
                        <div
                            class="flex items-center justify-center w-16 h-16 text-orange-600 transition-colors rounded-2xl bg-orange-50 group-hover:bg-orange-600 group-hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-800 group-hover:text-orange-700">مكتبة الدروس</span>
                    </a>
                </div>
            </div>

            <!-- 3. Sidebar (History & News) -->
            <div class="space-y-8">

                <!-- History -->
                @if(count($previousMeetings) > 0)
                    <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-3xl">
                        <h3 class="flex items-center gap-2 mb-4 text-xl font-black text-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" class="text-gray-400">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            الأرشيف
                        </h3>
                        <div class="space-y-3">
                            @foreach($previousMeetings as $meeting)
                                <a href="{{ route('meeting.record', $meeting->id) }}" wire:navigate
                                    class="flex items-center justify-between p-3 transition-colors border border-transparent rounded-2xl hover:bg-gray-50 group hover:border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg {{ $meeting->status == 'completed' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500' }}">
                                            {{ $meeting->status == 'completed' ? '✔' : '✕' }}
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-800">
                                                {{ \Carbon\Carbon::parse($meeting->week_date)->locale('ar')->isoFormat('D MMMM') }}
                                            </h4>
                                            <p class="text-xs text-gray-400 transition-colors group-hover:text-indigo-600">
                                                {{ $meeting->status == 'completed' ? 'عرض التفاصيل' : 'تم الإلغاء' }}
                                            </p>
                                        </div>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2"
                                        class="text-gray-300 group-hover:text-indigo-400">
                                        <path d="m9 18 6-6-6-6" />
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Latest News -->
                @if($latestPost)
                    <div class="p-6 text-white shadow-lg bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl">
                        <div class="flex items-center gap-2 mb-4 opacity-80">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <polyline points="17 8 12 3 7 8" />
                                <line x1="12" y1="3" x2="12" y2="15" />
                            </svg>
                            <span class="text-sm font-bold">آخر القرارات</span>
                        </div>
                        <h4 class="mb-2 text-xl font-black">{{ $latestPost->title }}</h4>
                        <p class="mb-4 text-sm leading-relaxed text-indigo-100 line-clamp-3">{{ $latestPost->content }}</p>
                        <a href="{{ route('announcements') }}" wire:navigate
                            class="inline-block px-4 py-2 text-sm font-bold transition-colors bg-white/20 hover:bg-white/30 rounded-xl backdrop-blur-sm">
                            قراءة المزيد
                        </a>
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>
