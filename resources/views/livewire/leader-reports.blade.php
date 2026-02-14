<div class="min-h-screen pb-24 bg-gray-50">

    <!-- Header -->
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-4xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 py-4 md:flex-row md:items-center md:h-20">
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center justify-center w-12 h-12 text-gray-700 transition-colors bg-gray-100 rounded-2xl hover:bg-gray-200" aria-label="الرئيسية">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-black text-gray-900">التقارير والمتابعة 📝</h1>
                        <p class="hidden text-sm font-medium text-gray-500 md:block">إرسال ومتابعة التقارير الدورية</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl px-4 py-8 mx-auto sm:px-6 lg:px-8">

        <!-- STATE 1: LOCK SCREEN -->
        @if($isLocked)
            <div class="flex flex-col items-center justify-center py-12 md:py-20 animate-fade-in-up">

                <div class="w-full max-w-md p-8 text-center bg-white border border-gray-100 shadow-xl md:p-12 rounded-3xl">
                    <div class="flex items-center justify-center w-24 h-24 mx-auto mb-6 text-indigo-600 rounded-full shadow-inner bg-indigo-50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>

                    <h2 class="mb-3 text-2xl font-black text-gray-900">منطقة محمية</h2>
                    <p class="mb-8 text-base font-medium leading-relaxed text-gray-500">
                        هذه البيانات خاصة بالقادة فقط.<br>أدخل كود الحماية للمتابعة.
                    </p>

                    <form wire:submit="unlock" class="flex flex-col gap-6">
                        <div class="relative">
                            <input type="password" inputmode="numeric" wire:model="pinAttempt"
                                   class="w-full text-center text-4xl tracking-[0.5em] font-black p-4 rounded-2xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition-all placeholder-gray-200 text-gray-800"
                                   placeholder="••••" autofocus>
                        </div>

                        @if($errorMsg)
                            <div class="flex items-center justify-center gap-2 p-3 font-bold text-red-600 bg-red-50 rounded-xl animate-shake">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                {{ $errorMsg }}
                            </div>
                        @endif

                        <button type="submit" class="flex items-center justify-center gap-2 py-4 text-xl font-black text-white transition-all transform bg-indigo-600 shadow-lg rounded-2xl shadow-indigo-200 hover:bg-indigo-700 hover:shadow-xl active:scale-95">
                            <span>فتح التقارير</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
                        </button>
                    </form>

                    <a href="{{ route('profile') }}" class="inline-block mt-8 text-sm font-bold text-gray-400 transition-colors hover:text-indigo-600 hover:underline">
                        نسيت الكود؟ اضغط هنا
                    </a>
                </div>
            </div>

        <!-- STATE 2: REPORTS DASHBOARD (UNLOCKED) -->
        @else
            <!-- Action Buttons Grid -->
            <div class="grid grid-cols-1 gap-6 mb-10 sm:grid-cols-2 animate-fade-in-down">
                <!-- Weekly Report Button -->
                <a href="{{ route('report.form', ['type' => 'weekly']) }}" wire:navigate class="flex items-center gap-5 p-6 transition-all bg-white border border-indigo-100 shadow-sm group rounded-3xl hover:border-indigo-400 hover:shadow-md active:scale-95">
                    <div class="flex items-center justify-center w-16 h-16 text-indigo-600 transition-colors border border-indigo-100 shadow-inner rounded-2xl bg-indigo-50 group-hover:bg-indigo-600 group-hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="m9 16 2 2 4-4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-800 transition-colors group-hover:text-indigo-700">تقرير أسبوعي</h3>
                        <p class="mt-1 text-sm font-medium text-gray-500">تسجيل متابعة التلمذة</p>
                    </div>
                </a>

                <!-- Monthly Report Button -->
                <a href="{{ route('report.form', ['type' => 'monthly']) }}" wire:navigate class="flex items-center gap-5 p-6 transition-all bg-white border border-orange-100 shadow-sm group rounded-3xl hover:border-orange-400 hover:shadow-md active:scale-95">
                    <div class="flex items-center justify-center w-16 h-16 text-orange-600 transition-colors border border-orange-100 shadow-inner rounded-2xl bg-orange-50 group-hover:bg-orange-500 group-hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-800 transition-colors group-hover:text-orange-700">تقرير شهري</h3>
                        <p class="mt-1 text-sm font-medium text-gray-500">إحصائيات ونسب الشهر</p>
                    </div>
                </a>
            </div>

            <!-- Archive Section -->
            <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-3xl">
                <div class="flex items-center justify-between p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="flex items-center gap-2 text-xl font-black text-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-gray-400"><path d="M21 8v13H3V8"/><path d="M1 3h22v5H1z"/><path d="M10 12h4"/></svg>
                        الأرشيف السابق
                    </h3>
                    <span class="px-3 py-1 text-sm font-bold text-gray-700 bg-gray-200 rounded-full">{{ count($reports) }}</span>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($reports as $report)
                        <a href="{{ route('report.view', $report->id) }}" wire:navigate class="block p-5 transition-colors hover:bg-indigo-50/50 group">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shadow-sm border border-gray-100 {{ $report->type == 'weekly' ? 'bg-indigo-100 text-indigo-600' : 'bg-orange-100 text-orange-600' }}">
                                        {{ $report->type == 'weekly' ? '📅' : '📊' }}
                                    </div>
                                    <div>
                                        <h4 class="mb-1 text-base font-bold text-gray-800 transition-colors group-hover:text-indigo-700">
                                            {{ $report->type == 'weekly' ? 'تقرير أسبوعي' : 'تقرير شهري' }}
                                        </h4>
                                        <div class="flex items-center gap-2 text-xs font-bold text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                            {{ \Carbon\Carbon::parse($report->report_date)->locale('ar')->isoFormat('D MMMM YYYY') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <div>
                                    @if($report->admin_reply_at)
                                        <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-bold px-3 py-1.5 rounded-full border border-green-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span class="hidden sm:inline">تم الرد</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-500 text-xs font-bold px-3 py-1.5 rounded-full border border-gray-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            <span class="hidden sm:inline">قيد المراجعة</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="py-16 text-center">
                            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 text-3xl rounded-full bg-gray-50 grayscale opacity-30">📭</div>
                            <p class="font-bold text-gray-500">لا توجد تقارير سابقة</p>
                            <p class="mt-1 text-sm text-gray-400">ابدأ بإنشاء تقريرك الأول اليوم</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</div>
