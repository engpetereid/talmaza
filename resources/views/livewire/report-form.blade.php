<div class="min-h-screen pb-24 bg-gray-50">

    <!-- Header -->
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 py-4 md:flex-row md:items-center md:h-20">
                <div class="flex items-center gap-4">
                <a href="{{ Auth::user()->role == 'admin'? route('admin.reports'): route('leader.reports') }}" wire:navigate class="flex items-center justify-center w-12 h-12 text-gray-700 transition-colors bg-gray-100 rounded-2xl hover:bg-gray-200" aria-label="عودة">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </a>
                    <div>
                        <h1 class="text-xl font-black leading-tight text-gray-900 md:text-2xl">
                            {{ $isReadOnly ? 'مراجعة التقرير' : ($type == 'weekly' ? 'إنشاء تقرير أسبوعي' : 'إنشاء تقرير شهري') }}
                        </h1>
                        <p class="mt-1 text-sm font-bold text-gray-500">{{ $family->name ?? '' }}</p>
                    </div>
                </div>

                @if($isReadOnly && Auth::user()->role == 'admin')
                    <button wire:click="saveAdminReply"
                            class="flex items-center gap-2 px-6 py-3 font-bold text-white transition-transform bg-green-600 shadow-lg hover:bg-green-700 rounded-xl shadow-green-200 active:scale-95">
                        <span wire:loading.remove wire:target="saveAdminReply">حفظ الردود</span>
                        <span wire:loading wire:target="saveAdminReply">جاري الحفظ...</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="px-4 py-8 mx-auto space-y-8 max-w-7xl sm:px-6 lg:px-8">

        @if (session()->has('message'))
            <div class="flex items-center gap-3 p-4 font-bold text-green-800 bg-green-100 border-r-4 border-green-500 rounded-xl animate-fade-in-down">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('message') }}
            </div>
        @endif

        <!-- ================= WEEKLY REPORT ================= -->
        @if($type == 'weekly')

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                <!-- Timeline Table -->
                <div class="p-6 bg-white border border-gray-200 shadow-sm lg:col-span-2 rounded-3xl">
                    <h3 class="flex items-center gap-2 pb-3 mb-4 text-lg font-black text-gray-800 border-b border-gray-100">
                        <span class="p-2 text-indigo-600 bg-indigo-100 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                        ترتيب لقاء التلمذة
                    </h3>
                    <div class="space-y-3">
                        @foreach($timeline as $index => $row)
                            <div class="flex items-center gap-3 group">
                                @if(!$isReadOnly)
                                    <input type="text" wire:model="timeline.{{ $index }}.time" placeholder="6-6:30"
                                           class="w-20 px-1 py-3 font-mono text-sm font-bold text-center border-2 border-gray-200 bg-gray-50 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">

                                    <input type="text" wire:model="timeline.{{ $index }}.activity" placeholder="اكتب النشاط هنا..."
                                           class="flex-grow px-2 py-3 text-sm font-bold border-2 border-gray-200 bg-gray-50 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">

                                    <button wire:click="removeTimelineRow({{ $index }})"
                                            class="flex items-center justify-center w-10 h-10 text-red-400 transition-colors hover:text-red-600 hover:bg-red-50 rounded-xl">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    </button>
                                @else
                                    <div class="flex items-center justify-between w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-xl">
                                        <span class="px-3 py-1 font-mono text-sm font-black text-indigo-700 bg-white border border-indigo-100 rounded-lg shadow-sm">
                                            {{ isset($row['time']) ? $row['time'] : ($row['start'] . ' - ' . $row['end']) }}
                                        </span>
                                        <span class="text-base font-bold text-gray-900">{{ $row['activity'] }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if(!$isReadOnly)
                        <button wire:click="addTimelineRow"
                                class="flex items-center justify-center w-full gap-2 py-3 mt-4 text-sm font-black text-indigo-600 transition-all border-2 border-indigo-200 border-dashed rounded-xl hover:bg-indigo-50 hover:border-indigo-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            إضافة فقرة جديدة
                        </button>
                    @endif
                </div>

                <!-- Visitation Hours -->
                <div class="flex flex-col items-center justify-center p-6 text-center bg-white border border-gray-200 shadow-sm lg:col-span-1 rounded-3xl">
                    <div class="flex items-center justify-center w-16 h-16 mb-4 text-3xl text-blue-600 border border-blue-100 shadow-sm bg-blue-50 rounded-2xl">
                        🏃‍♂️
                    </div>
                    <h3 class="mb-4 text-lg font-black text-gray-800">ساعات الافتقاد</h3>
                    @if(!$isReadOnly)
                        <div class="flex items-center justify-center gap-2">
                            <input type="number" step="0.5" min="0" wire:model="visitation_hours"
                                   class="w-24 text-3xl font-black text-center text-blue-700 border-2 border-gray-200 h-14 bg-gray-50 rounded-2xl focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="0">
                            <span class="text-sm font-bold text-gray-500">ساعة</span>
                        </div>
                        @error('visitation_hours') <span class="block mt-2 text-sm font-bold text-red-500">{{ $message }}</span> @enderror
                    @else
                        <div class="w-full px-6 py-4 border border-blue-100 bg-blue-50 rounded-2xl">
                            <span class="block text-4xl font-black text-blue-700">{{ $visitation_hours ?? 0 }}</span>
                            <span class="text-xs font-bold tracking-wider text-blue-500 uppercase">ساعة هذا الأسبوع</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Achievements List -->
            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-3xl">
                <h3 class="flex items-center gap-2 pb-3 mb-6 text-lg font-black text-gray-800 border-b border-gray-100">
                    <span class="p-2 text-green-600 bg-green-100 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></span>
                    ما تم تنفيذه وأفكار جديدة
                </h3>

                <div class="space-y-6">
                    @foreach($weekly_achievements as $index => $item)
                        <div class="group bg-gray-50 p-5 rounded-2xl border-2 {{ $isReadOnly && Auth::user()->role == 'admin' ? 'border-green-200 bg-green-50/30' : 'border-gray-200' }}">
                            @if(!$isReadOnly)
                                <div class="flex items-start gap-3">
                                    <div class="mt-3 text-xl font-black text-green-500">•</div>
                                    <div class="w-full">
                                        <textarea wire:model="weekly_achievements.{{ $index }}.text" rows="3"
                                                  class="w-full p-4 text-base font-medium leading-relaxed placeholder-gray-400 bg-white border-2 border-gray-200 rounded-xl focus:ring-green-500 focus:border-green-500"
                                                  placeholder="اكتب نقطة محددة..."></textarea>
                                        <div class="flex justify-end mt-2">
                                            <button wire:click="removeItem('weekly_achievements', {{ $index }})"
                                                    class="text-red-500 hover:text-red-700 text-xs font-bold flex items-center gap-1 transition-colors bg-white px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-red-50 hover:border-red-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                                حذف
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-start gap-3">
                                    <div class="mt-1 text-2xl font-black leading-none text-green-600">•</div>
                                    <p class="text-base font-bold leading-relaxed text-gray-900">{{ $item['text'] }}</p>
                                </div>
                            @endif

                            @if($isReadOnly)
                                @if(Auth::user()->role == 'admin')
                                    <div class="flex items-start gap-3 pt-3 mt-4 border-t border-green-200/50">
                                        <div class="px-2 py-1 mt-2 text-xs font-black text-green-800 bg-green-200 rounded">رد:</div>
                                        <textarea wire:model="weekly_achievements.{{ $index }}.reply" rows="2"
                                                  class="flex-grow p-3 text-sm font-medium bg-white border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-green-500"
                                                  placeholder="اكتب ردك هنا..."></textarea>
                                    </div>
                                @elseif(!empty($item['reply']))
                                    <div class="flex items-start gap-3 p-4 mt-4 bg-green-100 border border-green-200 rounded-xl">
                                        <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-800 bg-green-200 rounded-full">✍️</div>
                                        <div>
                                            <span class="block mb-1 text-xs font-black tracking-wider text-green-800 uppercase">الرد </span>
                                            <p class="text-sm font-bold leading-relaxed text-green-900">{{ $item['reply'] }}</p>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>

                @if(!$isReadOnly)
                    <button wire:click="addItem('weekly_achievements')"
                            class="flex items-center justify-center w-full gap-2 py-3 mt-6 text-sm font-black text-green-700 transition-all border-2 border-green-200 border-dashed bg-green-50 hover:bg-green-100 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        إضافة نقطة أخرى
                    </button>
                @endif
            </div>

        @endif

        <!-- ================= MONTHLY REPORT ================= -->
        @if($type == 'monthly')

            <!-- Month Selection -->
            @if(!$isReadOnly)
                <div class="flex flex-wrap items-center justify-between gap-4 p-6 mb-8 bg-white border border-gray-200 shadow-sm rounded-3xl">
                    <div class="flex items-center gap-4">
                        <div class="p-3 text-purple-600 bg-purple-100 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>
                        <div>
                            <label class="block text-lg font-black text-gray-900">شهر التقرير</label>
                            <p class="text-sm font-medium text-gray-500">سيتم حساب النسب تلقائياً بناءً على الشهر المختار</p>
                        </div>
                    </div>
                    <input type="month" wire:model.live="report_date_input"
                           class="w-full px-4 py-3 text-base font-bold text-gray-900 transition-colors border-2 border-gray-200 shadow-sm cursor-pointer sm:w-auto bg-gray-50 rounded-xl focus:ring-purple-500 focus:border-purple-500 hover:bg-white">
                </div>
            @endif

            <!-- 1. General Stats -->
            @if(!empty($stats_snapshot))
                <div class="bg-gradient-to-br from-indigo-800 to-purple-900 text-white p-6 md:p-8 rounded-[2rem] shadow-xl relative overflow-hidden mb-8 animate-fade-in-down">
                    <div class="relative z-10">
                        <div class="flex items-center justify-between pb-4 mb-8 border-b border-white/10">
                            <h3 class="flex items-center gap-3 text-xl font-black">
                                <div class="p-2 bg-white/20 rounded-xl"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                                إحصائيات شهر {{ $stats_snapshot['month_name'] ?? 'الحالي' }}
                            </h3>
                            <span class="px-4 py-2 text-sm font-bold border bg-white/20 rounded-xl border-white/10">{{ $stats_snapshot['meetings_count'] ?? 0 }} اجتماعات</span>
                        </div>

                        <!-- Snapshot Grid -->
                        <div class="grid grid-cols-2 gap-4 mb-8 md:grid-cols-4">
                            <div class="p-4 text-center border bg-white/10 rounded-2xl backdrop-blur-md border-white/10"><span class="block mb-1 text-3xl font-black">{{ $stats_snapshot['attendance'] ?? 0 }}%</span><span class="text-xs font-bold uppercase opacity-70">الحضور</span></div>
                            <div class="p-4 text-center border bg-white/10 rounded-2xl backdrop-blur-md border-white/10"><span class="block mb-1 text-3xl font-black">{{ $stats_snapshot['note'] ?? 0 }}%</span><span class="text-xs font-bold uppercase opacity-70">النوتة</span></div>
                            <div class="p-4 text-center border bg-white/10 rounded-2xl backdrop-blur-md border-white/10"><span class="block mb-1 text-3xl font-black">{{ $stats_snapshot['kholwa'] ?? 0 }}%</span><span class="text-xs font-bold uppercase opacity-70">الخلوة</span></div>
                            <div class="p-4 text-center border bg-white/10 rounded-2xl backdrop-blur-md border-white/10"><span class="block mb-1 text-3xl font-black ">{{ $stats_snapshot['training'] ?? 0 }}%</span><span class="text-xs font-bold text-yellow-100 uppercase opacity-70">التدريب</span></div>
                            <div class="p-3 text-center border bg-white/5 rounded-xl border-white/5"><span class="block text-xl font-bold">{{ $stats_snapshot['mass'] ?? 0 }}%</span><span class="text-[10px] opacity-60 font-bold">القداس</span></div>
                            <div class="p-3 text-center border bg-white/5 rounded-xl border-white/5"><span class="block text-xl font-bold">{{ $stats_snapshot['vespers'] ?? 0 }}%</span><span class="text-[10px] opacity-60 font-bold">عشية</span></div>
                            <div class="p-3 text-center border bg-white/5 rounded-xl border-white/5"><span class="block text-xl font-bold">{{ $stats_snapshot['reading'] ?? 0 }}%</span><span class="text-[10px] opacity-60 font-bold">قراءة</span></div>
                            <div class="p-3 text-center border bg-white/5 rounded-xl border-white/5"><span class="block text-xl font-bold">{{ $stats_snapshot['altar'] ?? 0 }}%</span><span class="text-[10px] opacity-60 font-bold">مذبح</span></div>
                        </div>

                        <!-- Detailed Table -->
                        <div class="overflow-hidden border bg-black/20 rounded-2xl border-white/10">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-center">
                                    <thead class="text-xs font-bold tracking-wider uppercase bg-black/20 text-white/80">
                                        <tr>
                                            <th class="p-4 text-right min-w-[140px]">الاسم</th>
                                            <th class="p-4">حضور</th>
                                            <th class="p-4">نوتة</th>
                                            <th class="p-4">قداس</th>
                                            <th class="p-4">عشية</th>
                                            <th class="p-4">خلوة</th>
                                            <th class="p-4">قراءة</th>
                                            <th class="p-4">تدريب</th>
                                            <th class="p-4">مذبح</th>
                                            <th class="p-4">اسبوعية</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-medium divide-y divide-white/10 text-white/90">
                                        @foreach($members_monthly_stats as $stat)
                                            <tr class="hover:bg-white/5 transition-colors {{ !$stat['is_active'] ? 'opacity-50 bg-black/10' : '' }}">
                                                <td class="p-3 font-bold text-right whitespace-nowrap">{{ $stat['name'] }}</td>
                                                <td class="p-3 font-bold">{{ $stat['attendance'] }}%</td>
                                                <td class="p-3">{{ $stat['note_score'] }}%</td>
                                                <td class="p-3">{{ $stat['has_mass'] }}%</td>
                                                <td class="p-3">{{ $stat['has_tasbeha'] }}%</td>
                                                <td class="p-3">{{ $stat['kholwa_count'] }}%</td>
                                                <td class="p-3">{{ $stat['has_reading'] }}%</td>
                                                <td class="p-3 font-bold text-yellow-300">{{ $stat['talmaza_training_count'] }}%</td>
                                                <td class="p-3">{{ $stat['has_family_altar'] }}%</td>
                                                <td class="p-3">{{ $stat['has_weekly_kholwa'] }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 3. Monthly Summary List -->
            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-3xl">
                <h3 class="flex items-center gap-2 pb-3 mb-6 text-lg font-black text-gray-800 border-b border-gray-100">
                    <span class="p-2 text-orange-600 bg-orange-100 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></span>
                    ما تم تنفيذه خلال الشهر
                </h3>

                <div class="space-y-6">
                    @foreach($monthly_summary as $index => $item)
                        <div class="p-5 border-2 border-gray-200 group bg-gray-50 rounded-2xl">
                            @if(!$isReadOnly)
                                <div class="flex items-start gap-3">
                                    <div class="mt-3 text-xl font-black text-orange-500">•</div>
                                    <div class="w-full">
                                        <textarea wire:model="monthly_summary.{{ $index }}.text" rows="3"
                                                  class="w-full p-4 text-base font-medium leading-relaxed placeholder-gray-400 bg-white border-2 border-gray-200 rounded-xl focus:ring-orange-500 focus:border-orange-500"
                                                  placeholder="اكتب نشاط أو درس..."></textarea>
                                        <div class="flex justify-end mt-2">
                                            <button wire:click="removeItem('monthly_summary', {{ $index }})"
                                                    class="text-red-500 hover:text-red-700 text-xs font-bold flex items-center gap-1 transition-colors bg-white px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-red-50 hover:border-red-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                                حذف
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-start gap-3">
                                    <div class="mt-1 text-2xl font-black leading-none text-orange-500">•</div>
                                    <p class="text-base font-bold leading-relaxed text-gray-900">{{ $item['text'] }}</p>
                                </div>
                            @endif

                            @if($isReadOnly)
                                @if(Auth::user()->role == 'admin')
                                    <div class="flex items-start gap-3 pt-3 mt-4 border-t border-orange-200/50">
                                        <div class="px-2 py-1 mt-2 text-xs font-black text-orange-800 bg-orange-200 rounded">رد:</div>
                                        <textarea wire:model="monthly_summary.{{ $index }}.reply" rows="2"
                                                  class="flex-grow p-3 text-sm font-medium bg-white border-2 border-orange-200 rounded-xl focus:border-orange-500 focus:ring-orange-500"
                                                  placeholder="تعليق ..."></textarea>
                                    </div>
                                @elseif(!empty($item['reply']))
                                    <div class="flex items-start gap-3 p-4 mt-4 bg-orange-100 border border-orange-200 rounded-xl">
                                        <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 text-orange-800 bg-orange-200 rounded-full">✍️</div>
                                        <div>
                                            <span class="block mb-1 text-xs font-black tracking-wider text-orange-800 uppercase">تعليق</span>
                                            <p class="text-sm font-bold leading-relaxed text-orange-900">{{ $item['reply'] }}</p>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>

                @if(!$isReadOnly)
                    <button wire:click="addItem('monthly_summary')"
                            class="flex items-center justify-center w-full gap-2 py-3 mt-6 text-sm font-black text-orange-700 transition-all border-2 border-orange-200 border-dashed bg-orange-50 hover:bg-orange-100 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        إضافة بند جديد
                    </button>
                @endif
            </div>

            <!-- 4. Member Notes -->
            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-3xl">
                <h3 class="flex items-center gap-2 pb-3 mb-6 text-lg font-black text-gray-800 border-b border-gray-100">
                    <span class="p-2 text-red-600 bg-red-100 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></span>
                    ملاحظات عن المخدومين
                </h3>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    @foreach($familyMembers as $member)
                        @if($member->is_active || (isset($members_notes[$member->id]['text']) && !empty($members_notes[$member->id]['text'])))
                            <div class="p-4 transition-colors border-2 border-gray-100 bg-gray-50 rounded-2xl hover:border-red-200">
                                <div class="flex items-center gap-3 pb-2 mb-2 border-b border-gray-200">
                                    <div class="flex items-center justify-center w-8 h-8 text-xs font-black text-gray-600 bg-white border border-gray-200 rounded-full">
                                        {{ mb_substr($member->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 {{ !$member->is_active ? 'line-through opacity-50' : '' }}">
                                        {{ $member->name }}
                                    </span>
                                </div>

                                @if(!$isReadOnly)
                                    <textarea wire:model="members_notes.{{ $member->id }}.text" rows="2"
                                              class="w-full p-3 text-sm font-medium bg-white border-2 border-gray-200 rounded-xl focus:ring-red-500 focus:border-red-500"
                                              placeholder="ملاحظة عن المخدوم..."></textarea>
                                @else
                                    <div class="bg-white border border-gray-200 p-3 rounded-xl text-sm text-gray-700 font-medium leading-relaxed min-h-[50px]">
                                        {{ $members_notes[$member->id]['text'] ?? 'لا توجد ملاحظات' }}
                                    </div>
                                @endif

                                @if($isReadOnly && !empty($members_notes[$member->id]['text']))
                                    @if(Auth::user()->role == 'admin')
                                        <div class="flex items-start gap-2 mt-3">
                                            <span class="text-[10px] bg-red-100 text-red-800 font-black px-2 py-1 rounded mt-1">توجيه:</span>
                                            <textarea wire:model="members_notes.{{ $member->id }}.reply" rows="2"
                                                      class="w-full p-2 text-xs font-medium border-red-200 rounded-lg focus:ring-red-500 bg-red-50"
                                                      placeholder="رد خاص..."></textarea>
                                        </div>
                                    @elseif(!empty($members_notes[$member->id]['reply']))
                                        <div class="flex gap-2 p-3 mt-3 text-xs border border-red-100 rounded-lg bg-red-50">
                                            <span class="font-black text-red-800 shrink-0">توجيه:</span>
                                            <span class="font-bold text-red-900">{{ $members_notes[$member->id]['reply'] }}</span>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- ================= COMMON: PRIEST MESSAGES ================= -->
        <div class="p-6 bg-white border border-blue-200 shadow-sm rounded-3xl">
            <h3 class="flex items-center gap-2 pb-3 mb-6 text-lg font-black text-gray-800 border-b border-gray-100">
                <span class="p-2 text-blue-600 bg-blue-100 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
                رسائل للأب الكاهن / الأمين
            </h3>

            <div class="space-y-6">
                @foreach($priest_message as $index => $item)
                    <div class="p-5 border-2 border-blue-100 group bg-blue-50/50 rounded-2xl">
                        @if(!$isReadOnly)
                            <div class="flex items-start gap-3">
                                <div class="w-full">
                                    <textarea wire:model="priest_message.{{ $index }}.text" rows="3"
                                              class="w-full p-4 text-base font-medium leading-relaxed placeholder-blue-300 bg-white border-2 border-blue-200 rounded-xl focus:ring-blue-500 focus:border-blue-500"
                                              placeholder="اكتب رسالتك..."></textarea>
                                    <div class="flex justify-end mt-2">
                                        <button wire:click="removeItem('priest_message', {{ $index }})"
                                                class="text-red-500 hover:text-red-700 text-xs font-bold flex items-center gap-1 transition-colors bg-white px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-red-50 hover:border-red-200 shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            حذف
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-base font-bold leading-relaxed text-gray-900">{{ $item['text'] }}</p>
                        @endif

                        @if($isReadOnly)
                            @if(Auth::user()->role == 'admin')
                                <div class="flex items-start gap-3 pt-3 mt-4 border-t border-blue-200">
                                    <div class="px-2 py-1 mt-2 text-xs font-black text-blue-800 bg-blue-200 rounded">رد:</div>
                                    <textarea wire:model="priest_message.{{ $index }}.reply" rows="2"
                                              class="flex-grow p-3 text-sm font-medium bg-white border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="الرد على هذه الرسالة..."></textarea>
                                </div>
                            @elseif(!empty($item['reply']))
                                <div class="flex items-start gap-3 p-4 mt-4 bg-blue-100 border border-blue-200 shadow-sm rounded-xl">
                                    <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 text-blue-800 bg-blue-200 rounded-full">💬</div>
                                    <div>
                                        <span class="block mb-1 text-xs font-black tracking-wider text-blue-800 uppercase">الرد</span>
                                        <p class="text-sm font-bold leading-relaxed text-blue-900">{{ $item['reply'] }}</p>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>

            @if(!$isReadOnly)
                <button wire:click="addItem('priest_message')"
                        class="flex items-center justify-center w-full gap-2 py-3 mt-6 text-sm font-black text-blue-700 transition-all border-2 border-blue-200 border-dashed bg-blue-50 hover:bg-blue-100 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    رسالة جديدة
                </button>
            @endif
        </div>

        <!-- Submit Button -->
        @if(!$isReadOnly)
            <div class="pt-6 pb-8">
                <button wire:click="save"
                        class="w-full bg-indigo-600 text-white py-5 rounded-2xl font-black shadow-xl shadow-indigo-200 hover:bg-indigo-700 hover:shadow-2xl transition-all transform active:scale-[0.98] text-lg flex items-center justify-center gap-3">
                    <span>حفظ وإرسال التقرير</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </div>
        @endif

    </div>
</div>
