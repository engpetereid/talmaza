<div class="min-h-screen pb-20 bg-gray-50">
    <script src="{{ asset('build/assets/chart.js')}}"></script>

    <!-- Header -->
    <div class="sticky top-0 z-10 p-4 mx-auto bg-white border-b border-gray-100 shadow-sm rounded-2xl"
        style="max-width: 1200px">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-lg font-bold text-gray-800">تقارير المراحل: {{ $family->name }} 📊</h1>
                <p class="text-xs text-gray-400">القائد:
                    {{ $family->users()->where('role', 'leader')->first()->name ?? '-' }}</p>
            </div>
            <a href="{{ route('admin.family.view', $family->id) }}" wire:navigate
                class="px-3 py-1 text-xs font-bold text-gray-600 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">رجوع
                للعائلة</a>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <!-- Stage Selector (Corrected) -->
            <div class="flex flex-grow gap-2 pb-2 overflow-x-auto no-scrollbar">
                @foreach ($stages as $stage)
                    <button wire:click="$set('selectedStageId', {{ $stage->id }})"
                        class="px-4 py-2 rounded-lg text-xs font-bold whitespace-nowrap transition-all border
                            {{ $selectedStageId == $stage->id ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50' }}">
                        {{ $stage->name }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="max-w-4xl p-4 mx-auto">

        @if(!$data)
            <div class="py-16 mt-6 text-center bg-white border border-gray-200 border-dashed rounded-2xl">
                <div class="mb-2 text-4xl">📚</div>
                <p class="font-bold text-gray-500">لا توجد بيانات لهذه المرحلة</p>
                <p class="mt-1 text-xs text-gray-400">تأكد من اختيار مرحلة بها اجتماعات مسجلة</p>
            </div>
        @else
            <!-- 1. Summary Cards -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="p-4 text-center bg-white border border-gray-100 shadow-sm rounded-2xl">
                    <span class="block mb-1 text-xs text-gray-400">دروس المرحلة</span>
                    <span class="text-2xl font-bold text-indigo-600">{{ $data['meetings_count'] }}</span>
                </div>
                <div class="p-4 text-center bg-white border border-gray-100 shadow-sm rounded-2xl">
                    <span class="block mb-1 text-xs text-gray-400">المخدومين</span>
                    <span class="text-2xl font-bold text-purple-600">{{ count($data['members_stats']) }}</span>
                </div>
            </div>

            <!-- 2. Performance Chart -->
            <div class="relative p-4 mb-6 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <h3 class="flex items-center gap-2 mb-4 text-sm font-bold text-gray-800">
                    <div class="w-1 h-4 bg-indigo-500 rounded-full"></div>
                    تطور المستوى خلال المرحلة
                </h3>
                <div class="w-full h-56"
                    wire:key="chart-stage-{{ $selectedStageId }}-{{ $showMeetingsList ? 'open' : 'closed' }}" x-data x-init='
                            if ($el.chart) { $el.chart.destroy(); }
                            $el.chart = new Chart($el.querySelector("canvas"), {
                                type: "line",
                                data: {
                                    labels: @json($data["chart_data"]["labels"]),
                                    datasets: [
                                        { label: "حضور", data: @json($data["chart_data"]["attendance"]), borderColor: "#3b82f6", backgroundColor: "#3b82f620", tension: 0.3, fill: true },
                                        { label: "نوتة", data: @json($data["chart_data"]["note"]), borderColor: "#a855f7", backgroundColor: "transparent", tension: 0.3, borderDash: [5, 5] },
                                        { label: "قداس", data: @json($data["chart_data"]["mass"]), borderColor: "#ea580c", backgroundColor: "transparent", tension: 0.3 }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: { legend: { position: "bottom", labels: { boxWidth: 10, font: { size: 15 } } } },
                                    scales: { y: { beginAtZero: true, max: 100, grid: { color: "#f3f4f6" } }, x: { grid: { display: false } } }
                                }
                            });
                         '>
                    <canvas></canvas>
                </div>
            </div>

            <!-- 3. Meetings List Toggle -->
            <div class="mb-6">
                <button wire:click="toggleMeetingsList"
                    class="flex items-center justify-center w-full gap-2 py-3 text-sm font-bold text-indigo-600 transition-all bg-white border border-indigo-100 shadow-sm rounded-xl hover:bg-indigo-50">
                    @if($showMeetingsList)
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m18 15-6-6-6 6" />
                        </svg>
                        إخفاء دروس المرحلة
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                        عرض دروس المرحلة ({{ $data['meetings_count'] }})
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
                                            {{ $meeting->lesson->title ?? 'درس بدون عنوان' }}
                                        </h4>
                                        <p class="text-[10px] text-gray-400">
                                            {{ \Carbon\Carbon::parse($meeting->week_date)->locale('ar')->isoFormat('D MMMM YYYY') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-gray-400">
                                    <span>عرض</span>
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

            <!-- 4. Detailed Member Table -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach($data['members_stats'] as $stat)
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden {{ !$stat['is_active'] ? 'opacity-70 bg-gray-50' : '' }}">

                        <!-- Header -->
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
                        <!-- Grid Details -->
                        <div class="grid grid-cols-4 gap-2 p-3 text-center sm:grid-cols-5">
                            <div class="col-span-1 p-1 rounded-lg bg-blue-50">
                                <div class="text-[9px] text-gray-500">حضور</div>
                                <div class="text-xs font-bold text-blue-700">{{ $stat['attendance'] }}%</div>
                            </div>
                            <div class="col-span-1 p-1 rounded-lg bg-purple-50">
                                <div class="text-[9px] text-gray-500">نوتة</div>
                                <div class="text-xs font-bold text-purple-700">{{ $stat['note'] }}%</div>
                            </div>
                            <div class="col-span-1 p-1 rounded-lg bg-orange-50">
                                <div class="text-[9px] text-gray-500">قداس</div>
                                <div class="text-xs font-bold text-orange-700">{{ $stat['mass'] }}%</div>
                            </div>
                            <div class="col-span-1 p-1 rounded-lg bg-pink-50">
                                <div class="text-[9px] text-gray-500">تدريب التلمذة</div>
                                <div class="text-xs font-bold text-pink-700">{{ $stat['training'] }}%</div>
                            </div>
                            <div class="hidden col-span-1 p-1 rounded-lg bg-teal-50 sm:block">
                                <div class="text-[9px] text-gray-500">قراءة</div>
                                <div class="text-xs font-bold text-teal-700">{{ $stat['reading'] }}%</div>
                            </div>

                            <div class="col-span-4 my-1 border-t border-gray-100 sm:col-span-5"></div>

                            <div class="col-span-1">
                                <div class="text-[9px] text-gray-400 font-bold">مشاركة الخلوة</div>
                                <div class="text-xs font-bold">{{ $stat['kholwa'] }}%</div>
                            </div>
                            <div class="col-span-1">
                                <div class="text-[9px] text-gray-400 font-bold">عشية</div>
                                <div class="text-xs font-bold">{{ $stat['vespers'] }}%</div>
                            </div>
                            <div class="col-span-1">
                                <div class="text-[9px] text-gray-400 font-bold">تسبحة</div>
                                <div class="text-xs font-bold">{{ $stat['tasbeha'] }}%</div>
                            </div>
                            <div class="col-span-1">
                                <div class="text-[9px] text-gray-400 font-bold">اجتماع خدام</div>
                                <div class="text-xs font-bold">{{ $stat['servants'] }}%</div>
                            </div>
                            <div class="col-span-1">
                                <div class="text-[9px] text-gray-400 font-bold">مذبح عائلى</div>
                                <div class="text-xs font-bold">{{ $stat['altar'] }}%</div>
                            </div>
                            <div class="col-span-1 ">
                                <div class="text-[9px] text-gray-400 font-bold">قراءة</div>
                                <div class="text-xs font-bold">{{ $stat['reading'] }}%</div>
                            </div>
                            <div class="col-span-1">
                                <div class="text-[9px] text-gray-400 font-bold">خلوه أسبوعية</div>
                                <div class="text-xs font-bold">{{ $stat['weekly_kholwa'] }}%</div>
                            </div>
                            <div class="col-span-1 ">
                                <div class="text-[9px] text-gray-400 font-bold">سماع العظة</div>
                                <div class="text-xs font-bold ">{{ $stat['sermon'] }}%</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
