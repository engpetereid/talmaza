<div class="min-h-screen pb-24 bg-gray-50">

    <!-- Header -->
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 py-4 md:flex-row md:items-center md:h-20">

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
                <div class="flex gap-2 pb-2 overflow-x-auto md:pb-0 no-scrollbar">
                    <button wire:click="setFilter('all')"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all border-2 {{ $filter == 'all' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-300' }}">
                        الكل
                    </button>
                    <button wire:click="setFilter('pending')"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all border-2 flex items-center gap-2 {{ $filter == 'pending' ? 'bg-red-500 text-white border-red-500 shadow-md' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-300' }}">
                        <span>⚠️</span> لم يتم الرد
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
            </div>
        </div>
    </div>

    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

        <!-- Reports Grid -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($reports as $report)
                            <a href="{{ route('report.view', $report->id) }}" wire:navigate
                                class="relative flex flex-col h-full overflow-hidden transition-all duration-300 bg-white border-2 border-gray-100 shadow-sm group rounded-3xl hover:border-indigo-300 hover:shadow-lg">

                                <!-- Status Banner -->
                                <div
                                    class="absolute top-0 right-0 left-0 h-1.5 {{ $report->admin_reply_at ? 'bg-green-500' : 'bg-red-500 animate-pulse' }}">
                                </div>

                                <div class="flex-grow p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <!-- Icon -->
                                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl shadow-sm border-2 transition-colors {{ $report->type == 'weekly' ? 'bg-indigo-50 border-indigo-100 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600' : 'bg-orange-50 border-orange-100 text-orange-600 group-hover:bg-orange-600 group-hover:text-white group-hover:border
                                        -orange-600' }}">
                                            {{ $report->type == 'weekly' ? '📅' : '📊' }}
                                        </div>

                                        <!-- Badge -->
                                        @if($report->admin_reply_at)
                                            <span
                                                class="flex items-center gap-1 px-3 py-1 text-xs font-bold text-green-700 bg-green-100 border border-green-200 rounded-full">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="3">
                                                    <polyline points="20 6 9 17 4 12" />
                                                </svg>
                                                تم الرد
                                            </span>
                                        @else
                                            <span
                                                class="flex items-center gap-1 px-3 py-1 text-xs font-bold text-red-700 bg-red-100 border border-red-200 rounded-full">
                                                <span class="relative flex w-2 h-2">
                                                    <span
                                                        class="absolute inline-flex w-full h-full bg-red-400 rounded-full opacity-75 animate-ping"></span>
                                                    <span class="relative inline-flex w-2 h-2 bg-red-500 rounded-full"></span>
                                                </span>
                                                بانتظار الرد
                                            </span>
                                        @endif
                                    </div>

                                    <h4 class="mb-1 text-xl font-bold text-gray-900 transition-colors group-hover:text-indigo-700">
                                        {{ $report->family->name ?? 'عائلة محذوفة' }}
                                    </h4>

                                    <p class="mb-4 text-sm font-medium text-gray-500">
                                        {{ $report->type == 'weekly' ? 'تقرير أسبوعي' : 'تقرير شهري' }}
                                    </p>

                                    <div
                                        class="flex items-center gap-2 p-3 text-xs font-bold text-gray-400 border border-gray-100 bg-gray-50 rounded-xl">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                            <line x1="16" y1="2" x2="16" y2="6" />
                                            <line x1="8" y1="2" x2="8" y2="6" />
                                            <line x1="3" y1="10" x2="21" y2="10" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($report->report_date)->locale('ar')->isoFormat('D MMMM YYYY') }}
                                        <span class="mx-1">•</span>
                                        منذ {{ $report->created_at->diffForHumans() }}
                                    </div>
                                </div>

                                <div class="p-3 text-center border-t border-gray-100 bg-gray-50">
                                    <span class="text-xs font-bold text-indigo-600 group-hover:underline">عرض التفاصيل والرد</span>
                                </div>
                            </a>
            @empty
                <div class="py-20 text-center bg-white border-2 border-gray-200 border-dashed col-span-full rounded-3xl">
                    <div
                        class="flex items-center justify-center w-24 h-24 mx-auto mb-6 text-5xl rounded-full opacity-50 bg-gray-50 grayscale">
                        📭</div>
                    <h3 class="mb-2 text-xl font-bold text-gray-800">صندوق الوارد فارغ</h3>
                    <p class="text-gray-500">لا توجد تقارير مطابقة للفلتر الحالي</p>
                    @if($filter !== 'all')
                        <button wire:click="setFilter('all')"
                            class="px-6 py-2 mt-6 font-bold text-indigo-600 hover:underline bg-indigo-50 rounded-xl">عرض كل
                            التقارير</button>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-10">
            {{ $reports->links() }}
        </div>

    </div>
</div>
