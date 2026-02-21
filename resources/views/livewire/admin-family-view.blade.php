<div class="min-h-screen pb-20 bg-gray-50">

    <!-- Header -->
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 py-4 md:flex-row md:items-center md:h-24">

                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" wire:navigate
                        class="flex items-center justify-center w-12 h-12 text-gray-700 transition-colors bg-gray-100 rounded-2xl hover:bg-gray-200"
                        aria-label="عودة">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-black leading-tight text-gray-900 md:text-3xl">{{ $family->name }}</h1>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-sm font-bold text-gray-500 md:text-base">خادم العائلة:</span>
                            <span
                                class="bg-indigo-50 text-indigo-700 px-3 py-0.5 rounded-lg text-sm font-bold border border-indigo-100">
                                {{ $family->users()->where('role', 'leader')->first()->name ?? 'غير محدد' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Desktop Report Actions (Visible only on PC) -->
                <div class="hidden gap-3 md:flex">
                    <a href="{{ route('admin.family.stats', $family->id) }}" wire:navigate
                        class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl font-bold transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5">
                            <line x1="18" y1="20" x2="18" y2="10" />
                            <line x1="12" y1="20" x2="12" y2="4" />
                            <line x1="6" y1="20" x2="6" y2="14" />
                        </svg>
                        تقارير النسب الشهرية
                    </a>
                    <a href="{{ route('admin.family.stage_stats', $family->id) }}" wire:navigate
                        class="flex items-center gap-2 px-5 py-3 font-bold text-orange-600 transition-all bg-white border-2 border-orange-100 hover:border-orange-200 hover:bg-orange-50 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5">
                            <path d="M22 10v6M2 10v6" />
                            <path
                                d="M20 21a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3zm2-5a2 2 0 0 0-2-2h-2V8a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v4H4a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2z" />
                        </svg>
                        تقارير المنهج
                    </a>

                    <!-- Desktop Delete Button -->
                    <button wire:click="deleteFamily"
                        wire:confirm="تحذير هام: هل أنت متأكد من حذف هذه العائلة؟ سيتم حذف جميع المخدومين وسجلاتهم نهائياً. لا يمكن التراجع عن هذا الإجراء."
                        class="flex items-center gap-2 px-5 py-3 font-bold text-red-600 transition-all border-2 border-red-100 bg-red-50 hover:bg-red-600 hover:text-white rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5">
                            <path d="M3 6h18" />
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                        </svg>
                        حذف العائلة
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 py-8 mx-auto space-y-8 max-w-7xl sm:px-6 lg:px-8">

        <!-- Mobile Report Actions (Visible only on Mobile) -->
        <div class="grid grid-cols-2 gap-4 md:hidden">
            <a href="{{ route('admin.family.stats', $family->id) }}" wire:navigate
                class="flex flex-col items-center justify-center gap-2 p-4 text-center transition-colors bg-white border border-indigo-100 shadow-sm rounded-2xl hover:bg-indigo-50">
                <div class="flex items-center justify-center w-12 h-12 text-indigo-600 bg-indigo-100 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5">
                        <line x1="18" y1="20" x2="18" y2="10" />
                        <line x1="12" y1="20" x2="12" y2="4" />
                        <line x1="6" y1="20" x2="6" y2="14" />
                    </svg>
                </div>
                <span class="text-sm font-bold text-gray-800">النسب الشهرية</span>
            </a>
            <a href="{{ route('admin.family.stage_stats', $family->id) }}" wire:navigate
                class="flex flex-col items-center justify-center gap-2 p-4 text-center transition-colors bg-white border border-orange-100 shadow-sm rounded-2xl hover:bg-orange-50">
                <div class="flex items-center justify-center w-12 h-12 text-orange-600 bg-orange-100 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5">
                        <path d="M22 10v6M2 10v6" />
                        <path
                            d="M20 21a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3zm2-5a2 2 0 0 0-2-2h-2V8a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v4H4a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2z" />
                    </svg>
                </div>
                <span class="text-sm font-bold text-gray-800">تقارير المنهج</span>
            </a>
        </div>

        <!-- Feedback Messages -->
        @if (session()->has('status'))
            <div
                class="flex items-center gap-2 p-4 font-bold text-green-800 bg-green-100 border-r-4 border-green-500 rounded-lg shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                {{ session('status') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div
                class="flex items-center gap-2 p-4 font-bold text-yellow-800 bg-yellow-100 border-r-4 border-yellow-500 rounded-lg shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                    <line x1="12" y1="9" x2="12" y2="13" />
                    <line x1="12" y1="17" x2="12.01" y2="17" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- 1. Members List -->
        <div>
            <div class="flex items-center gap-3 mb-6">
                <h3 class="text-xl font-black text-gray-800">قائمة المخدومين</h3>
                <span
                    class="px-3 py-1 text-sm font-bold text-gray-700 bg-gray-200 rounded-full">{{ $members->count() }}</span>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse($members as $member)
                    <div
                        class="group bg-white p-5 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md hover:border-indigo-300 transition-all duration-300 relative {{ !$member->is_active ? 'bg-gray-50 border-gray-200' : '' }}">

                        <div class="flex items-start gap-4">
                            <!-- Avatar -->
                            <a href="{{ route('member.stats', $member->id) }}" wire:navigate class="flex-shrink-0 block">
                                <div
                                    class="w-16 h-16 rounded-2xl flex items-center justify-center font-black text-2xl shadow-sm border-2 overflow-hidden transition-colors {{ $member->is_active ? 'bg-indigo-50 text-indigo-600 border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white' : 'bg-gray-200 text-gray-400 border-gray-300' }}">
                                    @if($member->photo_path)
                                        <img src="{{ asset('storage/' . $member->photo_path) }}" alt="{{ $member->name }}"
                                            class="object-cover w-full h-full">
                                    @else
                                        {{ mb_substr($member->name, 0, 1) }}
                                    @endif
                                </div>
                            </a>

                            <div class="flex-grow pt-1">
                                <!-- Name -->
                                <a href="{{ route('member.stats', $member->id) }}" wire:navigate class="block">
                                    <h4
                                        class="font-bold text-lg text-gray-900 group-hover:text-indigo-600 transition-colors {{ !$member->is_active ? 'line-through opacity-60' : '' }}">
                                        {{ $member->name }}
                                    </h4>
                                </a>

                                <!-- Details -->
                                <div class="mt-2 space-y-1">
                                    @if($member->phone)
                                        <div class="flex items-center gap-1.5 text-sm font-medium text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path
                                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                            </svg>
                                            <span dir="ltr">{{ $member->phone }}</span>
                                        </div>
                                    @endif
                                    @if($member->birth_date)
                                        <div class="flex items-center gap-1.5 text-sm font-medium text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                                <line x1="16" y1="2" x2="16" y2="6" />
                                                <line x1="8" y1="2" x2="8" y2="6" />
                                                <line x1="3" y1="10" x2="21" y2="10" />
                                            </svg>
                                            <span>{{ \Carbon\Carbon::parse($member->birth_date)->age }} سنة</span>
                                        </div>
                                    @endif

                                    @if(!$member->is_active)
                                        <span
                                            class="inline-block bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded mt-1">غير
                                            نشط</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Delete Action -->
                            <div class="flex-shrink-0">
                                <button
                                    wire:confirm="هل أنت متأكد من حذف هذا العضو؟ إذا كان له سجلات سيتم فقط إلغاء تنشيطه."
                                    wire:click="deleteMember({{ $member->id }})"
                                    class="flex items-center justify-center w-10 h-10 text-gray-400 transition-colors rounded-xl bg-gray-50 hover:bg-red-50 hover:text-red-600"
                                    title="حذف أو إلغاء تنشيط">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M3 6h18" />
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="py-16 text-center bg-white border-2 border-gray-200 border-dashed col-span-full rounded-3xl">
                        <div
                            class="flex items-center justify-center w-20 h-20 mx-auto mb-4 text-4xl text-gray-300 rounded-full bg-gray-50">
                            👥</div>
                        <p class="text-lg font-bold text-gray-500">لا يوجد مخدومين في هذه العائلة</p>
                        <p class="mt-1 text-sm text-gray-400">يمكن للقائد إضافة مخدومين من لوحته الخاصة</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 2. Latest Meetings Section (New) -->
        <div class="pt-8 border-t border-gray-200">
            <div class="flex items-center gap-3 mb-6">
                <h3 class="text-xl font-black text-gray-800">آخر 4 اجتماعات</h3>
                <span
                    class="px-3 py-1 text-sm font-bold text-teal-700 bg-teal-100 rounded-full">{{ $meetings->count() }}</span>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @forelse($meetings as $meeting)
                    <a href="{{ route('meeting.record', ['meeting' => $meeting->id, 'readonly' => 1]) }}" wire:navigate
                        class="relative block p-5 transition-all bg-white border border-gray-100 shadow-sm group rounded-2xl hover:shadow-md hover:border-teal-300">

                        <!-- Status Strip -->
                        <div
                            class="absolute right-0 top-4 bottom-4 w-1 rounded-l-full {{ $meeting->status == 'completed' ? 'bg-teal-500' : 'bg-red-400' }}">
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 flex items-center justify-center w-12 h-12 text-2xl border shadow-sm rounded-xl border-gray-50 {{ $meeting->status == 'completed' ? 'bg-teal-50 text-teal-600' : 'bg-red-50 text-red-600' }}">
                                {{ $meeting->status == 'completed' ? '✔' : '✕' }}
                            </div>

                            <div>
                                <h4 class="text-base font-bold text-gray-800 transition-colors group-hover:text-teal-600">
                                    {{ $meeting->lesson->title ?? $meeting->custom_topic ?? 'بدون عنوان' }}
                                </h4>
                                <div class="flex items-center gap-2 mt-1 text-xs font-medium text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                    {{ \Carbon\Carbon::parse($meeting->week_date)->locale('ar')->isoFormat('D MMMM YYYY') }}
                                </div>
                            </div>
                        </div>

                        <!-- Badge -->
                        <div class="flex justify-end mt-3">
                            <span
                                class="inline-flex items-center gap-1 text-[10px] bg-gray-50 text-gray-600 px-2 py-1 rounded-md font-bold border border-gray-200 group-hover:bg-teal-50 group-hover:text-teal-700 group-hover:border-teal-100 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                عرض التفاصيل
                            </span>
                        </div>
                    </a>
                @empty
                    <div
                        class="py-10 text-center bg-white border-2 border-gray-200 border-dashed col-span-full rounded-2xl">
                        <p class="font-medium text-gray-900">لا توجد اجتماعات مسجلة لهذه العائلة</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 3. Latest Reports Section -->
        <div class="pt-8 border-t border-gray-200">
            <div class="flex items-center gap-3 mb-6">
                <h3 class="text-xl font-black text-gray-800">آخر 10 تقارير</h3>
                <span
                    class="px-3 py-1 text-sm font-bold text-blue-700 bg-blue-100 rounded-full">{{ $reports->count() }}</span>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @forelse($reports as $report)
                    <a href="{{ route('report.view', $report->id) }}" wire:navigate
                        class="relative block p-5 transition-all bg-white border border-gray-100 shadow-sm group rounded-2xl hover:shadow-md hover:border-indigo-300">

                        <!-- Status Strip -->
                        <div
                            class="absolute right-0 top-4 bottom-4 w-1 rounded-l-full {{ $report->admin_reply_at ? 'bg-green-500' : 'bg-orange-400' }}">
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 flex items-center justify-center w-12 h-12 text-2xl border shadow-sm rounded-xl border-gray-50 {{ $report->type == 'weekly' ? 'bg-indigo-50 text-indigo-600' : 'bg-orange-50 text-orange-600' }}">
                                {{ $report->type == 'weekly' ? '📅' : '📊' }}
                            </div>

                            <div>
                                <h4 class="text-base font-bold text-gray-800 transition-colors group-hover:text-indigo-600">

                                    {{ $report->type == 'weekly' ? 'تقرير أسبوعي' : 'تقرير شهري' }}
                                </h4>
                                <div class="flex items-center gap-2 mt-1 text-xs font-medium text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                    {{ \Carbon\Carbon::parse($report->report_date)->locale('ar')->isoFormat('D MMMM YYYY') }}
                                </div>
                            </div>
                        </div>

                        <!-- Badge -->
                        <div class="flex justify-end mt-3">
                            @if($report->admin_reply_at)
                                <span
                                    class="inline-flex items-center gap-1 text-[10px] bg-green-50 text-green-700 px-2 py-1 rounded-md font-bold border border-green-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2.5">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                    تم الرد
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1 text-[10px] bg-orange-50 text-orange-700 px-2 py-1 rounded-md font-bold border border-orange-100">
                                    بانتظار الرد
                                </span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div
                        class="py-10 text-center bg-white border-2 border-gray-200 border-dashed col-span-full rounded-2xl">
                        <p class="font-medium text-gray-900">لا توجد تقارير سابقة لهذه العائلة</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Danger Zone (Mobile Only) -->
        <div class="pt-8 mt-8 border-t border-gray-200 md:hidden">
            <h3 class="flex items-center gap-2 mb-4 text-lg font-black text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5">
                    <path
                        d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                    <line x1="12" y1="9" x2="12" y2="13" />
                    <line x1="12" y1="17" x2="12.01" y2="17" />
                </svg>
                منطقة الخطر
            </h3>
            <button wire:click="deleteFamily"
                wire:confirm="تحذير هام: هل أنت متأكد من حذف هذه العائلة؟ سيتم حذف جميع المخدومين وسجلاتهم نهائياً. لا يمكن التراجع عن هذا الإجراء."
                class="flex items-center justify-center w-full gap-2 px-5 py-4 font-bold text-red-600 transition-all border-2 border-red-100 bg-red-50 hover:bg-red-600 hover:text-white rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5">
                    <path d="M3 6h18" />
                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                </svg>
                حذف العائلة نهائياً
            </button>
        </div>

    </div>
</div>
