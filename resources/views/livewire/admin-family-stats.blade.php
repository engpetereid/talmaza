<div class="min-h-screen pb-20 bg-gray-50">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Header -->
    <div class="sticky top-0 z-10 p-4 mx-auto bg-white border-b border-gray-100 shadow-sm rounded-2xl" style="max-width: 1000px">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-lg font-bold text-gray-800">تقرير: {{ $family->name }} 📊</h1>
                <p class="text-xs text-gray-400">القائد:
                    {{ $family->users()->where('role', 'leader')->first()->name ?? '-' }}
                </p>
            </div>
            <a href="{{ route('admin.family.view', $family->id) }}" wire:navigate
                class="px-3 py-1 text-xs font-bold text-gray-600 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">رجوع
                للعائلة</a>
        </div>

        <!-- Filters Row -->
        <div class="flex flex-col gap-3 sm:flex-row">
            <!-- Year Selector -->
            <select wire:model.live="year"
                class="block w-full p-2 text-xs font-bold text-gray-700 border-gray-200 rounded-lg bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500 sm:w-auto">
                @foreach($availableYears as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>

            <!-- Month Selector -->
            <div class="flex flex-grow gap-2 pb-2 overflow-x-auto no-scrollbar">
                @for ($i = 1; $i <= 12; $i++)
                    <button wire:click="$set('month', {{ $i }})"
                        class="px-4 py-2 rounded-lg text-xs font-bold whitespace-nowrap transition-all border
                                                            {{ $month == $i ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50' }}">
                        {{ Carbon\Carbon::create()->month($i)->locale('ar')->monthName }}
                    </button>
                @endfor
            </div>
        </div>
    </div>

    <div class="max-w-4xl p-4 mx-auto">
        @if(!$data)
            <div class="py-16 mt-6 text-center bg-white border border-gray-200 border-dashed rounded-2xl">
                <div class="mb-2 text-4xl">📅</div>
                <p class="font-bold text-gray-500">لا توجد بيانات لهذا الشهر</p>
                <p class="mt-1 text-xs text-gray-400">حاول اختيار شهر أو سنة مختلفة</p>
            </div>
        @else
            <!-- 1. Summary Cards -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="p-4 text-center bg-white border border-gray-100 shadow-sm rounded-2xl">
                    <span class="block mb-1 text-xs text-gray-400">اجتماعات الشهر</span>
                    <span class="text-2xl font-bold text-indigo-600">{{ $data['meetings_count'] }}</span>
                </div>
                <div class="p-4 text-center bg-white border border-gray-100 shadow-sm rounded-2xl">
                    <span class="block mb-1 text-xs text-gray-400">المخدومين</span>
                    <span class="text-2xl font-bold text-purple-600">{{ count($data['members_stats']) }}</span>
                </div>
            </div>

            <!-- 2. Meetings List Toggle -->
            <div class="mb-6">
                <button wire:click="toggleMeetingsList"
                    class="flex items-center justify-center w-full gap-2 py-3 text-sm font-bold text-indigo-600 transition-all bg-white border border-indigo-100 shadow-sm rounded-xl hover:bg-indigo-50">
                    @if($showMeetingsList)
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m18 15-6-6-6 6" />
                        </svg>
                        إخفاء اجتماعات الشهر
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                        عرض اجتماعات الشهر ({{ $data['meetings_count'] }})
                    @endif
                </button>

                @if($showMeetingsList)
                    <div class="mt-3 space-y-2 animate-slide-down">
                        @foreach($data['meetings'] as $meeting)
                            <a href="{{ route('meeting.record', ['meeting' => $meeting->id, 'readonly' => 1]) }}"
                                class="flex items-center justify-between block p-3 transition-all bg-white border border-gray-100 shadow-sm rounded-xl hover:border-indigo-300">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 text-lg font-bold text-green-600 rounded-full bg-green-50">
                                        ✔</div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-800">
                                            {{ \Carbon\Carbon::parse($meeting->week_date)->locale('ar')->isoFormat('D MMMM') }}
                                        </h4>
                                        <p class="text-[10px] text-gray-400">
                                            {{ $meeting->lesson->title ?? $meeting->custom_topic ?? 'بدون عنوان' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-gray-400">
                                    <span>عرض فقط</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- 3. Detailed Member Table -->
            <div class="space-y-4">
                @foreach($data['members_stats'] as $stat)
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden {{ !$stat['is_active'] ? 'opacity-70 bg-gray-50' : '' }}">
                        <a href="{{ route('member.stats', $stat['id']) }}"
                            class="flex items-center justify-between p-4 border-b border-gray-50 bg-gray-50/50">
                            <!-- Link to Member Page -->
                            <div class="flex items-center gap-2 group">
                                <div
                                    class="flex items-center justify-center w-8 h-8 text-xs font-bold text-gray-500 transition-colors bg-white border border-gray-100 rounded-full shadow-sm group-hover:border-indigo-300 group-hover:text-indigo-600">
                                    {{ mb_substr($stat['name'], 0, 1) }}
                                </div>
                                <div>
                                    <h3
                                        class="text-sm font-bold text-gray-800 transition-colors group-hover:text-indigo-700 group-hover:underline">
                                        {{ $stat['name'] }}
                                    </h3>
                                    @if(!$stat['is_active']) <span class="text-[9px] text-red-500 block">غير نشط</span> @endif
                                </div>
                            </div>
                            <span
                                class="px-2 py-1 rounded-md text-xs font-bold {{ $stat['total_average'] >= 50 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $stat['total_average'] }}%</span>
                        </a>

                        <!-- شبكة التفاصيل -->
                        <div class="grid grid-cols-4 gap-2 p-3 text-center sm:grid-cols-5">
                            <div class="col-span-1 p-1 rounded-lg bg-blue-50">
                                <div class="text-[9px] text-gray-900">حضور</div>
                                <div class="text-xs font-bold text-blue-700">{{ $stat['attendance'] }}%</div>
                            </div>
                            <div class="col-span-1 p-1 rounded-lg bg-purple-50">
                                <div class="text-[9px] text-gray-900">نوتة</div>
                                <div class="text-xs font-bold text-purple-700">{{ $stat['note'] }}%</div>
                            </div>
                            <div class="col-span-1 p-1 rounded-lg bg-orange-50">
                                <div class="text-[9px] text-gray-900">القداس</div>
                                <div class="text-xs font-bold text-orange-700">{{ $stat['mass'] }}%</div>
                            </div>
                            <div class="col-span-1 p-1 rounded-lg bg-pink-50">
                                <div class="text-[9px] text-gray-900">تدريب التلمذة</div>
                                <div class="text-xs font-bold text-pink-700">{{ $stat['training'] }}%</div>
                            </div>
                            <div class="hidden col-span-1 p-1 rounded-lg bg-teal-50 sm:block">
                                <div class="text-[9px] text-gray-900">قراءة</div>
                                <div class="text-xs font-bold text-teal-700">{{ $stat['reading'] }}%</div>
                            </div>

                            <div class="col-span-4 my-1 border-t border-gray-100 sm:col-span-5"></div>

                            <div class="col-span-1">
                                <div class="text-[9px] text-gray-900">مشاركة الخلوة</div>
                                <div class="text-xs font-bold">{{ $stat['kholwa'] }}%</div>
                            </div>
                            <div class="col-span-1">
                                <div class="text-[9px] text-gray-900">تسبحة</div>
                                <div class="text-xs font-bold">{{ $stat['vespers'] }}%</div>
                            </div>
                            <div class="col-span-1">
                                <div class="text-[9px] text-gray-900">اجتماع الخدام</div>
                                <div class="text-xs font-bold">{{ $stat['servants'] }}%</div>
                            </div>
                            <div class="col-span-1">
                                <div class="text-[9px] text-gray-900">مذبح عائلى</div>
                                <div class="text-xs font-bold">{{ $stat['altar'] }}%</div>
                            </div>
                            <div class="col-span-1 sm:hidden">
                                <div class="text-[9px] text-gray-900">قراءة</div>
                                <div class="text-xs font-bold">{{ $stat['reading'] }}%</div>
                            </div>
                            <div class="hidden col-span-1 sm:block">
                                <div class="text-[9px] text-gray-900">خلوة أسبوعية</div>
                                <div class="text-xs font-bold">{{ $stat['weekly_kholwa'] }}%</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- 4. All Charts Section -->
            <div class="mt-8 space-y-6">
                <h3 class="flex items-center gap-2 px-2 pt-6 text-lg font-bold text-gray-800 border-t border-gray-200">
                    <div class="p-1.5 bg-indigo-100 text-indigo-600 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3v18h18" />
                            <path d="m19 9-5 5-4-4-3 3" />
                        </svg>
                    </div>
                    الرسم البياني
                </h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2" wire:key="charts-grid-{{ $month }}-{{ $year }}">
                    @foreach($metricsConfig as $key => $config)
                        <div class="p-4 bg-white border border-gray-100 shadow-sm rounded-2xl">
                            <h4 class="flex items-center justify-between mb-3 text-sm font-bold text-gray-700">
                                {{ $config['label'] }}
                                <span class="w-3 h-3 rounded-full" style="background-color: {{ $config['color'] }}"></span>
                            </h4>
                            <div class="relative w-full h-40" x-data x-init='
                                                                                                if ($el.chart) { $el.chart.destroy(); }
                                                                                                $el.chart = new Chart($el.querySelector("canvas"), {
                                                                                                    type: "line",
                                                                                                    data: {
                                                                                                        labels: @json($data["chart_data"]["labels"]),
                                                                                                        datasets: [{
                                                                                                            label: "{{ $config["label"] }}",
                                                                                                            data: @json($data["chart_data"][$key]),
                                                                                                            borderColor: "{{ $config["color"] }}",
                                                                                                            backgroundColor: "{{ $config["color"] }}20",
                                                                                                            tension: 0.3,
                                                                                                            fill: true,
                                                                                                            pointRadius: 2
                                                                                                        }]
                                                                                                    },
                                                                                                    options: {
                                                                                                        responsive: true,
                                                                                                        maintainAspectRatio: false,
                                                                                                        plugins: { legend: { display: false } },
                                                                                                        scales: {
                                                                                                            y: { beginAtZero: true, max: 100, ticks: { display: false }, grid: { display: false } },
                                                                                                            x: { grid: { display: false }, ticks: { font: { size: 9 } } }
                                                                                                        }
                                                                                                    }
                                                                                                });
                                                                                            '>
                                <canvas></canvas>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
