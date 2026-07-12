<div class="min-h-screen pb-24 bg-gray-50">

    <!-- Header -->
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 py-4 md:flex-row md:items-start md:h-auto">

                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" wire:navigate
                       class="flex items-center justify-center w-12 h-12 text-gray-700 transition-colors bg-gray-100 rounded-2xl hover:bg-gray-200"
                       aria-label="عودة للوحة التحكم">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-black text-gray-900">صندوق التقارير 📥</h1>
                        <p class="hidden text-sm font-medium text-gray-500 md:block">متابعة تقارير الخدمة والرد عليها
                        </p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-col gap-3">
                    <!-- Type Filters -->
                    <div class="flex gap-2 overflow-x-auto no-scrollbar">
                        <button wire:click="setFilter('all')"
                                class="px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all border-2 {{ $filter == 'all' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-300' }}">
                            الكل
                        </button>
                        <button wire:click="setFilter('pending')"
                                class="px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all border-2 flex items-center gap-2 {{ $filter == 'pending' ? 'bg-red-500 text-white border-red-500 shadow-md' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-300' }}">
                            <span>⚠️</span> يحتاج لرد
                        </button>
                        <button wire:click="setFilter('weekly')"
                                class="px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all border-2 {{ $filter == 'weekly' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-300' }}">
                            أسبوعي 📅
                        </button>
                        <button wire:click="setFilter('monthly')"
                                class="px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all border-2 {{ $filter == 'monthly' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-300' }}">
                            شهري 📊
                        </button>
                    </div>

                    <!-- Date Filters (Month & Year) -->
                    <div class="flex gap-2 overflow-x-auto no-scrollbar">
                        <select wire:model.live="filterYear" class="bg-gray-50 border-2 border-gray-200 text-gray-700 text-sm font-bold rounded-xl focus:ring-indigo-500 focus:border-indigo-500 py-2 px-4">
                            @foreach($availableYears as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                            <option value="all">كل السنين</option>
                        </select>

                        <select wire:model.live="filterMonth" class="bg-gray-50 border-2 border-gray-200 text-gray-700 text-sm font-bold rounded-xl focus:ring-indigo-500 focus:border-indigo-500 py-2 px-4">
                            @for($i=1; $i<=12; $i++)
                                <option value="{{ $i }}">{{ \Carbon\Carbon::create()->month($i)->locale('ar')->monthName }}</option>
                            @endfor
                            <option value="all">كل الشهور</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div
                class="flex items-center gap-3 p-4 mb-6 font-bold text-green-800 bg-green-100 border-r-4 border-green-500 shadow-sm rounded-xl animate-fade-in-down">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                {{ session('message') }}
            </div>
        @endif

        <!-- Reports Grid -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($reports as $report)
                @php
                    $isPending = is_null($report->admin_reply_at);
                    // تحديد إذا كان القائد قام بالرد بعد آخر رد للإدارة
                    $hasNewLeaderReply = !$isPending && $report->updated_at->gt($report->admin_reply_at);
                @endphp

                    <!-- Changed to DIV instead of Anchor to avoid nested click issues with Delete button -->
                <div
                    class="relative flex flex-col h-full overflow-hidden transition-all duration-300 bg-white border-2 border-gray-100 shadow-sm group rounded-3xl hover:border-indigo-300 hover:shadow-lg">

                    <!-- Status Banner -->
                    <div class="absolute top-0 right-0 left-0 h-1.5 {{ $isPending ? 'bg-red-500 animate-pulse' : ($hasNewLeaderReply ? 'bg-orange-500 animate-pulse' : 'bg-green-500') }}"></div>

                    <div class="flex-grow p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <!-- Icon -->
                                <div
                                    class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl shadow-sm border-2 transition-colors {{ $report->type == 'weekly' ? 'bg-indigo-50 border-indigo-100 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600' : 'bg-orange-50 border-orange-100 text-orange-600 group-hover:bg-orange-600 group-hover:text-white group-hover:border-orange-600' }}">
                                    {{ $report->type == 'weekly' ? '📅' : '📊' }}
                                </div>

                                <!-- Delete Action -->
                                <button wire:click="deleteReport({{ $report->id }})"
                                        wire:confirm="هل أنت متأكد من حذف هذا التقرير نهائياً؟"
                                        class="flex items-center justify-center w-10 h-10 text-gray-400 transition-colors bg-white rounded-xl hover:bg-red-50 hover:text-red-600"
                                        title="حذف التقرير">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round">
                                        <path d="M3 6h18" />
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Badges -->
                            @if($isPending)
                                <span class="flex items-center gap-1 px-3 py-1 text-xs font-bold text-red-700 bg-red-100 border border-red-200 rounded-full">
                                    <span class="relative flex w-2 h-2">
                                        <span class="absolute inline-flex w-full h-full bg-red-400 rounded-full opacity-75 animate-ping"></span>
                                        <span class="relative inline-flex w-2 h-2 bg-red-500 rounded-full"></span>
                                    </span>
                                    بانتظار الرد
                                </span>
                            @elseif($hasNewLeaderReply)
                                <span class="flex items-center gap-1 px-3 py-1 text-xs font-bold text-orange-700 bg-orange-100 border border-orange-200 rounded-full">
                                    <span class="relative flex w-2 h-2">
                                        <span class="absolute inline-flex w-full h-full bg-orange-400 rounded-full opacity-75 animate-ping"></span>
                                        <span class="relative inline-flex w-2 h-2 bg-orange-500 rounded-full"></span>
                                    </span>
                                    تعقيب القائد
                                </span>
                            @else
                                <span class="flex items-center gap-1 px-3 py-1 text-xs font-bold text-green-700 bg-green-100 border border-green-200 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12" /></svg>
                                    تم الرد
                                </span>
                            @endif
                        </div>

                        <!-- Clickable Content Area -->
                        <a  href="{{ $report->type == 'weekly' ? route('report.weekly.view',$report->id) : route('report.monthly.view', $report->id) }}" wire:navigate
                           class="block transition-opacity group-hover:opacity-80">
                            <h4 class="mb-1 text-xl font-bold text-gray-900 transition-colors group-hover:text-indigo-700">
                                {{ $report->family->name ?? 'عائلة محذوفة' }}

                            </h4>
                            <h3 class="font-bold ">
                                القائد
                                {{ $report->family->user->name }}
                            </h3>

                            <p class="mb-4 text-sm font-medium text-gray-500">
                                {{ $report->type == 'weekly' ? 'تقرير أسبوعي' : 'تقرير شهري' }}
                            </p>

                            <div
                                class="flex items-center gap-2 p-3 text-xs font-bold text-gray-400 border border-gray-100 bg-gray-50 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" />
                                    <line x1="8" y1="2" x2="8" y2="6" />
                                    <line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                                {{ \Carbon\Carbon::parse($report->report_date)->locale('ar')->isoFormat('D MMMM YYYY') }}
                                <span class="mx-1">•</span>
                                آخر نشاط: {{ $report->updated_at->diffForHumans() }}
                            </div>
                        </a>
                    </div>

                    <!-- Footer Link -->
                    <a  href="{{ $report->type == 'weekly' ? route('report.weekly.view',$report->id) : route('report.monthly.view', $report->id) }}" wire:navigate
                       class="block p-3 text-center transition-colors border-t border-gray-100 bg-gray-50 hover:bg-gray-100">
                        <span class="text-xs font-bold text-indigo-600 group-hover:underline">عرض التفاصيل والرد</span>
                    </a>
                </div>
            @empty
                <div class="py-20 text-center bg-white border-2 border-gray-200 border-dashed col-span-full rounded-3xl">
                    <div
                        class="flex items-center justify-center w-24 h-24 mx-auto mb-6 text-5xl rounded-full opacity-50 bg-gray-50 grayscale">
                        📭</div>
                    <h3 class="mb-2 text-xl font-bold text-gray-800">صندوق الوارد فارغ</h3>
                    <p class="text-gray-500">لا توجد تقارير مطابقة للبحث أو للفلتر المختار</p>
                    @if($filter !== 'all' || $filterMonth !== 'all' || $filterYear !== 'all')
                        <button wire:click="setFilter('all'); $set('filterMonth', 'all'); $set('filterYear', 'all')"
                                class="px-6 py-2 mt-6 font-bold text-indigo-600 transition-colors hover:underline bg-indigo-50 hover:bg-indigo-100 rounded-xl">عرض
                            كل التقارير والمواعيد</button>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-10">
            {{ $reports->links() }}
        </div>

        <!-- Missing Monthly Reports Section -->
        @if($filter === 'monthly')
            <div class="pt-8 mt-16 border-t border-gray-200 animate-fade-in-up">
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex items-center justify-center w-12 h-12 text-2xl text-red-600 bg-red-100 shadow-sm rounded-2xl">
                        ⚠️
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-800">عائلات لم ترسل التقرير الشهري</h3>
                        <p class="text-sm font-bold text-gray-500 mt-0.5">
                            لشهر {{ $filterMonth !== 'all' ? \Carbon\Carbon::create()->month((int)$filterMonth)->locale('ar')->monthName : 'الكل' }}
                            {{ $filterYear !== 'all' ? $filterYear : '' }}
                        </p>
                    </div>
                    <span class="px-5 py-1 mr-auto text-sm font-bold text-red-700 bg-red-100 rounded-full">{{ $missingFamilies->count() }}  </span>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @forelse($missingFamilies as $missingFamily)
                        <div class="flex items-center justify-between p-5 bg-white border border-gray-100 shadow-sm rounded-2xl hover:border-red-200 transition-colors group">
                            <div>
                                <h4 class="text-lg font-bold text-gray-900">{{ $missingFamily->name }}</h4>
                                <p class="flex items-center gap-1 mt-1 text-sm font-medium text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    القائد: {{ $missingFamily->user->first()->name ?? 'غير محدد' }}
                                </p>
                            </div>


                        </div>
                    @empty
                        <div class="py-12 text-center border-2 border-green-200 border-dashed col-span-full bg-green-50 rounded-2xl">
                            <div class="mb-3 text-4xl">🎉</div>
                            <h4 class="text-lg font-bold text-green-800">رائع جداً!</h4>
                            <p class="font-medium text-green-600">جميع العائلات أرسلت التقرير الشهري لهذا التاريخ.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

    </div>
</div>
