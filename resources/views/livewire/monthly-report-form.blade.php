<div class="min-h-screen pb-24 bg-gray-50">
    <!-- Header -->
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 py-4 md:flex-row md:items-center md:h-20">
                <div class="flex items-center gap-4">
                    <a href="{{ Auth::user()->role == 'admin'? route('admin.reports'): route('leader.reports') }}" wire:navigate class="flex items-center justify-center w-12 h-12 text-gray-700 transition-colors bg-gray-100 rounded-2xl hover:bg-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </a>
                    <div>
                        <h1 class="text-xl font-black leading-tight text-gray-900 md:text-2xl">{{ $isReadOnly ? 'مراجعة التقرير الشهري' : 'إنشاء تقرير شهري' }}</h1>
                        <p class="mt-1 text-sm font-bold text-gray-500">{{ $family->name ?? '' }}</p>
                    </div>
                </div>

                @if($isReadOnly)
                    <div class="flex gap-3">

                        @if(Auth::user()->role == 'admin')
                            <button wire:click="closeReplies" class="flex items-center gap-2 px-6 py-3 font-bold text-white transition-transform bg-blue-600 shadow-lg hover:bg-blue-700 rounded-xl shadow-blue-50 active:scale-95">
                                <span wire:loading.remove wire:target="closeReplies">إغلاق التقرير</span>
                                <span wire:loading wire:target="closeReplies">جاري...</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            </button>
                        @endif
                    </div>
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

        <!-- Month Selection (Create Mode) -->
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
                <input type="month" wire:model.live="report_date_input" class="w-full px-4 py-3 text-base font-bold text-gray-900 border-2 rounded-xl sm:w-auto bg-gray-50 transition-colors focus:ring-purple-500 focus:border-purple-500">
            </div>
        @endif

        <!-- 1. General Stats (Rich UI & Threaded Replies) -->
        @if(!empty($stats_snapshot))
            <div class="bg-gradient-to-br from-indigo-800 to-purple-900 text-white p-6 md:p-8 rounded-[2.5rem] shadow-xl relative overflow-hidden mb-8 animate-fade-in-down border border-indigo-700">
                <!-- Decorative Circles -->
                <div class="absolute top-0 right-0 w-64 h-64 -mt-20 -mr-20 bg-white rounded-full opacity-5 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 -mb-20 -ml-20 bg-white rounded-full opacity-5 blur-3xl"></div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between pb-4 mb-8 border-b border-white/10">
                        <h3 class="flex items-center gap-3 text-xl font-black">
                            <div class="p-2 bg-white/20 rounded-xl backdrop-blur-sm"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                            إحصائيات شهر {{ $stats_snapshot['month_name'] ?? 'الحالي' }}
                        </h3>
                        <span class="px-4 py-2 text-sm font-bold border bg-white/20 rounded-xl border-white/10 backdrop-blur-sm">{{ $stats_snapshot['meetings_count'] ?? 0 }} اجتماعات</span>
                    </div>

                    <!-- Snapshot Grid (12 Items) -->
                    <div class="grid grid-cols-2 gap-3 mb-8 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 text-center">
                        <div class="p-3 border bg-white/10 rounded-2xl backdrop-blur-md border-white/10 hover:bg-white/20 transition-colors"><span class="block mb-1 text-2xl font-black">{{ $stats_snapshot['attendance'] ?? 0 }}%</span><span class="text-xs font-bold uppercase opacity-80">الحضور</span></div>
                        <div class="p-3 border bg-white/10 rounded-2xl backdrop-blur-md border-white/10 hover:bg-white/20 transition-colors"><span class="block mb-1 text-2xl font-black">{{ $stats_snapshot['note'] ?? 0 }}%</span><span class="text-xs font-bold uppercase opacity-80">النوتة</span></div>
                        <div class="p-3 border bg-white/10 rounded-2xl backdrop-blur-md border-white/10 hover:bg-white/20 transition-colors"><span class="block mb-1 text-2xl font-black">{{ $stats_snapshot['kholwa'] ?? 0 }}%</span><span class="text-xs font-bold uppercase opacity-80">مشاركة الخلوة</span></div>
                        <div class="p-3 border bg-white/10 rounded-2xl backdrop-blur-md border-white/10 hover:bg-white/20 transition-colors"><span class="block mb-1 text-2xl font-black ">{{ $stats_snapshot['training'] ?? 0 }}%</span><span class="text-xs font-bold uppercase opacity-80">التلمذة</span></div>
                        <div class="p-3 border bg-white/10 rounded-2xl backdrop-blur-md border-white/10 hover:bg-white/20 transition-colors"><span class="block mb-1 text-2xl font-black">{{ $stats_snapshot['mass'] ?? 0 }}%</span><span class="text-xs font-bold uppercase opacity-80">القداس</span></div>
                        <div class="p-3 border bg-white/10 rounded-2xl backdrop-blur-md border-white/10 hover:bg-white/20 transition-colors"><span class="block mb-1 text-2xl font-black">{{ $stats_snapshot['vespers'] ?? 0 }}%</span><span class="text-xs font-bold uppercase opacity-80">عشية</span></div>
                        <div class="p-3 border bg-white/10 rounded-2xl backdrop-blur-md border-white/10 hover:bg-white/20 transition-colors"><span class="block mb-1 text-2xl font-black">{{ $stats_snapshot['tasbeha'] ?? 0 }}%</span><span class="text-xs font-bold uppercase opacity-80">تسبحة</span></div>
                        <div class="p-3 border bg-white/10 rounded-2xl backdrop-blur-md border-white/10 hover:bg-white/20 transition-colors"><span class="block mb-1 text-2xl font-black">{{ $stats_snapshot['servants'] ?? 0 }}%</span><span class="text-xs font-bold uppercase opacity-80">اجتماع خدام</span></div>
                        <div class="p-3 border bg-white/10 rounded-2xl backdrop-blur-md border-white/10 hover:bg-white/20 transition-colors"><span class="block mb-1 text-2xl font-black">{{ $stats_snapshot['reading'] ?? 0 }}%</span><span class="text-xs font-bold uppercase opacity-80">قراءة</span></div>
                        <div class="p-3 border bg-white/10 rounded-2xl backdrop-blur-md border-white/10 hover:bg-white/20 transition-colors"><span class="block mb-1 text-2xl font-black">{{ $stats_snapshot['altar'] ?? 0 }}%</span><span class="text-xs font-bold uppercase opacity-80">مذبح عائلى</span></div>
                        <div class="p-3 border bg-white/10 rounded-2xl backdrop-blur-md border-white/10 hover:bg-white/20 transition-colors"><span class="block mb-1 text-2xl font-black">{{ $stats_snapshot['weekly_kholwa'] ?? 0 }}%</span><span class="text-xs font-bold uppercase opacity-80">الخلوة الاسبوعية</span></div>
                        <div class="p-3 border bg-white/10 rounded-2xl backdrop-blur-md border-white/10 hover:bg-white/20 transition-colors"><span class="block mb-1 text-2xl font-black">{{ $stats_snapshot['sermon'] ?? 0 }}%</span><span class="text-xs font-bold uppercase opacity-80">العظة</span></div>
                    </div>

                    <!-- Detailed Table -->
                    <div class="overflow-auto max-h-[500px] border bg-black/20 rounded-2xl border-white/10 custom-scrollbar mb-6 relative">
                        <!-- 1. ضفنا هنا border-separate border-spacing-0 -->
                        <table class="w-full text-sm text-center border-separate border-spacing-0">
                            <thead class="text-xs uppercase text-white/80 font-bold tracking-wider">
                            <tr>
                                <!-- 2. حطينا sticky top-0 z-20 و لون خلفية صلب bg-indigo-950 على كل th -->
                                <th class="sticky top-0 z-20 p-4 text-right min-w-[140px] bg-indigo-950 border-b border-white/10 shadow-sm">الاسم</th>
                                <th class="sticky top-0 z-20 p-4 min-w-[70px] bg-indigo-950 border-b border-white/10 shadow-sm">حضور</th>
                                <th class="sticky top-0 z-20 p-4 min-w-[70px] bg-indigo-950 border-b border-white/10 shadow-sm">نوتة</th>
                                <th class="sticky top-0 z-20 p-4 min-w-[70px] bg-indigo-950 border-b border-white/10 shadow-sm">قداس</th>
                                <th class="sticky top-0 z-20 p-4 min-w-[70px] bg-indigo-950 border-b border-white/10 shadow-sm">عشية</th>
                                <th class="sticky top-0 z-20 p-4 min-w-[70px] bg-indigo-950 border-b border-white/10 shadow-sm">تسبحة</th>
                                <th class="sticky top-0 z-20 p-4 min-w-[90px] bg-indigo-950 border-b border-white/10 shadow-sm">مشاركة خلوة</th>
                                <th class="sticky top-0 z-20 p-4 min-w-[70px] bg-indigo-950 border-b border-white/10 shadow-sm">قراءة</th>
                                <th class="sticky top-0 z-20 p-4 min-w-[90px] bg-indigo-950 border-b border-white/10 shadow-sm">اجتماع خدام</th>
                                <th class="sticky top-0 z-20 p-4 min-w-[90px] bg-indigo-950 border-b border-white/10 shadow-sm">تلمذة</th>
                                <th class="sticky top-0 z-20 p-4 min-w-[80px] bg-indigo-950 border-b border-white/10 shadow-sm">مذبح</th>
                                <th class="sticky top-0 z-20 p-4 min-w-[90px] bg-indigo-950 border-b border-white/10 shadow-sm">خلوة اسبوعية</th>
                                <th class="sticky top-0 z-20 p-4 min-w-[70px] bg-indigo-950 border-b border-white/10 shadow-sm">العظة</th>
                            </tr>
                            </thead>
                            <tbody class="text-white font-bold">
                            @foreach($members_monthly_stats as $stat)
                                <tr class="hover:bg-white/10 transition-colors {{ !$stat['is_active'] ? 'opacity-50 bg-black/20' : '' }}">
                                    <!-- ضفنا border-b علشان الجدول يفضل شكله مقسم بعد ما شلنا divide-y -->
                                    <td class="p-4 text-right whitespace-nowrap border-b border-white/5">{{ $stat['name'] }}</td>
                                    <td class="p-4 border-b border-white/5">{{ $stat['attendance'] }}%</td>
                                    <td class="p-4 border-b border-white/5">{{ $stat['note_score'] }}%</td>
                                    <td class="p-4 border-b border-white/5">{{ $stat['mass'] ?? 0 }}%</td>
                                    <td class="p-4 border-b border-white/5">{{ $stat['vespers'] ?? 0 }}%</td>
                                    <td class="p-4 border-b border-white/5">{{ $stat['tasbeha'] ?? 0 }}%</td>
                                    <td class="p-4 border-b border-white/5">{{ $stat['kholwa_count'] }}%</td>
                                    <td class="p-4 border-b border-white/5">{{ $stat['reading'] ?? 0 }}%</td>
                                    <td class="p-4 border-b border-white/5">{{ $stat['servants'] ?? 0 }}%</td>
                                    <td class="p-4 text-yellow-300 border-b border-white/5">{{ $stat['talmaza_training_count'] }}%</td>
                                    <td class="p-4 border-b border-white/5">{{ $stat['altar'] ?? 0}}%</td>
                                    <td class="p-4 border-b border-white/5">{{ $stat['weekly_kholwa'] ?? 0 }}%</td>
                                    <td class="p-4 border-b border-white/5">{{ $stat['sermon'] ?? 0 }}%</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- 👈 Threaded Replies for General Stats -->
                    @if($isReadOnly)
                        <div class="bg-black/20 border border-white/10 rounded-2xl p-6 backdrop-blur-sm">
                            <h4 class="text-white font-bold text-base mb-4 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                تعليقات على إحصائيات الشهر
                            </h4>

                            <div class="space-y-4">
                                @foreach($stats_replies ?? [] as $reply)
                                    <div class="flex items-start gap-3 p-4 rounded-xl {{ $reply['role'] == 'admin' ? 'bg-white/10 border border-white/20' : 'bg-indigo-900/50 border border-indigo-400/30' }}">
                                        <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-full {{ $reply['role'] == 'admin' ? 'bg-white/20 text-white' : 'bg-indigo-400 text-white' }}">
                                            {{ $reply['role'] == 'admin' ? '👨‍💼' : '👤' }}
                                        </div>
                                        <div>
                                            <span class="block mb-1 text-xs font-black tracking-wider uppercase opacity-80">
                                                {{ $reply['name'] ?? ($reply['role'] == 'admin' ? 'الإدارة' : 'القائد') }}
                                            </span>
                                            <p class="text-sm font-bold leading-relaxed text-white">{{ $reply['text'] }}</p>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Input for NEW reply on Stats -->
                                <div class="flex items-start gap-3 pt-4 border-t border-white/10 mt-2">
                                    <div class="px-2 py-1 mt-2 text-xs font-black text-indigo-900 bg-white rounded shrink-0">تعقيب:</div>
                                    <textarea wire:model="stats_new_reply" rows="2"
                                              class="flex-grow p-3 text-sm font-bold bg-white/10 border-2 border-white/20 rounded-xl focus:border-white focus:ring-0 text-white placeholder-white/50"
                                               placeholder="أضف تعليقاً أو استفساراً حول هذه الإحصائيات..."></textarea>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- 2. Monthly Summary List -->
        <div class="p-6 md:p-8 bg-white border border-gray-200 shadow-sm rounded-[2rem]">
            <h3 class="flex items-center gap-2 pb-4 mb-6 text-xl font-black text-gray-800 border-b border-gray-100">
                <span class="p-2 text-orange-600 bg-orange-100 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></span>
                ما تم تنفيذه خلال الشهر
            </h3>

            <div class="space-y-6">
                @foreach($monthly_summary as $index => $item)
                    <div class="p-5 border-2 border-gray-100 group bg-gray-50 rounded-2xl transition-colors hover:border-orange-200">
                        @if(!$isReadOnly)
                            <div class="flex items-start gap-3">
                                <div class="mt-3 text-xl font-black text-orange-500">•</div>
                                <div class="w-full">
                                    <textarea wire:model="monthly_summary.{{ $index }}.text" rows="3"
                                              class="w-full p-4 text-base font-medium leading-relaxed placeholder-gray-400 bg-white border-2 border-gray-200 rounded-xl focus:ring-orange-500 focus:border-orange-500"
                                              placeholder="اكتب نشاط أو درس أو فعالية..."></textarea>
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

                        <!-- Threaded Replies -->
                        @if($isReadOnly)
                            @foreach($item['replies'] ?? [] as $reply)
                                <div class="flex items-start gap-3 p-4 mt-4 rounded-xl {{ $reply['role'] == 'admin' ? 'bg-indigo-100 border border-indigo-200' : 'bg-orange-100 border border-orange-200' }}">
                                    <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-full {{ $reply['role'] == 'admin' ? 'text-indigo-800 bg-indigo-200' : 'text-orange-800 bg-orange-200' }}">
                                         {{ $reply['role'] == 'admin' ? '👨‍💼' : '👤' }}
                                    </div>
                                    <div>
                                        <span class="block mb-1 text-xs font-black tracking-wider uppercase {{ $reply['role'] == 'admin' ? 'text-indigo-800' : 'text-orange-800' }}">
                                            {{ $reply['name'] ?? ($reply['role'] == 'admin' ? 'الإدارة' : 'القائد') }}
                                        </span>
                                        <p class="text-sm font-bold leading-relaxed {{ $reply['role'] == 'admin' ? 'text-indigo-900' : 'text-orange-900' }}">{{ $reply['text'] }}</p>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Input for NEW reply -->
                            <div class="flex items-start gap-3 pt-3 mt-4 border-t border-orange-200/50">
                                <div class="px-2 py-1 mt-2 text-xs font-black text-orange-800 bg-orange-200 rounded shrink-0">رد جديد:</div>
                                <textarea wire:model="monthly_summary.{{ $index }}.new_reply" rows="2"
                                          class="flex-grow p-3 text-sm font-medium bg-white border-2 border-orange-200 rounded-xl focus:border-orange-500 focus:ring-orange-500"
                                          placeholder="أضف تعقيباً..."></textarea>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if(!$isReadOnly)
                <button wire:click="addItem('monthly_summary')"
                        class="flex items-center justify-center w-full gap-2 py-4 mt-6 text-sm font-black text-orange-700 transition-all border-2 border-orange-200 border-dashed bg-orange-50 hover:bg-orange-100 rounded-2xl">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    إضافة بند جديد
                </button>
            @endif
        </div>

        <!-- 3. Member Notes -->
        <div class="p-6 md:p-8 bg-white border border-gray-200 shadow-sm rounded-[2rem]">
            <h3 class="flex items-center gap-2 pb-4 mb-6 text-xl font-black text-gray-800 border-b border-gray-100">
                <span class="p-2 text-red-600 bg-red-100 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></span>
                ملاحظات عن المخدومين
            </h3>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                @foreach($familyMembers as $member)
                    @if($member->is_active || !empty($members_notes[$member->id]['text']))
                        <div class="p-5 transition-colors border-2 border-gray-100 bg-gray-50 rounded-2xl hover:border-red-200">
                            <div class="flex items-center gap-3 pb-3 mb-3 border-b border-gray-200">
                                <div class="flex items-center justify-center w-10 h-10 text-sm font-black text-gray-600 bg-white border border-gray-200 rounded-full shadow-sm">
                                    {{ mb_substr($member->name, 0, 1) }}
                                </div>
                                <span class="text-base font-bold text-gray-900 {{ !$member->is_active ? 'line-through opacity-50' : '' }}">
                                    {{ $member->name }}
                                </span>
                            </div>
                            @if(!$isReadOnly)
                                <textarea wire:model="members_notes.{{ $member->id }}.text" rows="2"
                                          class="w-full p-4 text-sm font-medium bg-white border-2 border-gray-200 rounded-xl focus:ring-red-500 focus:border-red-500 placeholder-gray-400"
                                          placeholder="اكتب ملاحظة أو توجيه بخصوص المخدوم..."></textarea>
                            @else
                                <div class="p-4 border border-gray-200 bg-white rounded-xl text-sm font-bold text-gray-800 leading-relaxed min-h-[60px]">
                                    {{ $members_notes[$member->id]['text'] ?? 'لا توجد ملاحظات' }}
                                </div>
                            @endif

                            <!-- Threaded Replies for Member Notes -->
                            @if($isReadOnly && !empty($members_notes[$member->id]['text']))
                                <div class="space-y-3 mt-4">
                                    @foreach($members_notes[$member->id]['replies'] ?? [] as $reply)
                                        <div class="flex items-start gap-2 p-3 text-xs border rounded-xl {{ $reply['role'] == 'admin' ? 'bg-red-50 border-red-100' : 'bg-indigo-50 border-indigo-100' }}">
                                            <span class="font-black shrink-0 {{ $reply['role'] == 'admin' ? 'text-red-800' : 'text-indigo-800' }}"> {{ $reply['name'] ?? 'الإدارة' }}:</span>
                                            <span class="font-bold leading-relaxed {{ $reply['role'] == 'admin' ? 'text-red-900' : 'text-indigo-900' }}">{{ $reply['text'] }}</span>
                                        </div>
                                    @endforeach

                                    <!-- Input for NEW reply -->
                                    <div class="flex items-start gap-2 pt-2">
                                        <span class="text-[10px] bg-red-100 text-red-800 font-black px-2 py-1.5 rounded-lg shrink-0">رد:</span>
                                        <textarea wire:model="members_notes.{{ $member->id }}.new_reply" rows="2"
                                                  class="w-full p-3 text-xs font-bold border-2 border-red-200 rounded-xl focus:ring-red-500 focus:border-red-500 bg-red-50/50"
                                                  placeholder="إضافة تعقيب..."></textarea>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- 4. Priest Messages -->
        <div class="p-6 md:p-8 bg-white border-2 shadow-sm rounded-[2rem] border-blue-200">
            <h3 class="flex items-center gap-2 pb-4 mb-6 text-xl font-black text-gray-800 border-b border-gray-100">
                <span class="p-2 text-blue-600 bg-blue-100 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
                رسائل للأب الكاهن / الأمين
            </h3>

            <div class="space-y-6">
                @foreach($priest_message as $index => $item)
                    <div class="p-5 border-2 border-blue-100 group bg-blue-50/50 rounded-2xl transition-colors hover:border-blue-300">
                        @if(!$isReadOnly)
                            <div class="flex items-start gap-3">
                                <div class="w-full">
                                    <textarea wire:model="priest_message.{{ $index }}.text" rows="3"
                                               class="w-full p-4 text-base font-medium leading-relaxed placeholder-blue-300 bg-white border-2 border-blue-200 rounded-xl focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="اكتب رسالتك، استفسارك، أو مشكلة تواجهك..."></textarea>
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
                            <div class="flex items-start gap-3">
                                <div class="mt-1 text-xl font-black leading-none text-blue-500">✉️</div>
                                <p class="text-base font-bold leading-relaxed text-gray-900">{{ $item['text'] }}</p>
                            </div>
                        @endif

                        <!-- Threaded Replies -->
                        @if($isReadOnly)
                            @foreach($item['replies'] ?? [] as $reply)
                                <div class="flex items-start gap-3 p-4 mt-4 rounded-xl shadow-sm border {{ $reply['role'] == 'admin' ? 'bg-indigo-100 border-indigo-200' : 'bg-blue-100 border-blue-200' }}">
                                    <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-full {{ $reply['role'] == 'admin' ? 'text-indigo-800 bg-indigo-200' : 'text-blue-800 bg-blue-200' }}">
                                        💬
                                    </div>
                                    <div>
                                        <span class="block mb-1 text-xs font-black tracking-wider uppercase {{ $reply['role'] == 'admin' ? 'text-indigo-800' : 'text-blue-800' }}">
                                            رد {{ $reply['name'] ?? ($reply['role'] == 'admin' ? 'الإدارة' : 'القائد') }}
                                        </span>
                                        <p class="text-sm font-bold leading-relaxed {{ $reply['role'] == 'admin' ? 'text-indigo-900' : 'text-blue-900' }}">{{ $reply['text'] }}</p>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Input for NEW reply -->
                            <div class="flex items-start gap-3 pt-4 mt-4 border-t border-blue-200/50">
                                <div class="px-2 py-1 mt-2 text-xs font-black text-blue-800 bg-blue-200 rounded shrink-0">رد جديد:</div>
                                <textarea wire:model="priest_message.{{ $index }}.new_reply" rows="2"
                                          class="flex-grow p-3 text-sm font-medium bg-white border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="الرد على هذه الرسالة..."></textarea>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if(!$isReadOnly)
                 <button wire:click="addItem('priest_message')"
                                                   class="flex items-center justify-center w-full gap-2 py-4 mt-6 text-sm font-black text-blue-700 transition-all border-2 border-blue-200 border-dashed bg-blue-50 hover:bg-blue-100 rounded-2xl">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    إضافة رسالة جديدة
                </button>
            @endif



        </div>

        @if(!$isReadOnly)
            <div class="pt-8 pb-8">
                <button wire:click="save"
                        class="w-full bg-indigo-600 text-white py-5 rounded-2xl font-black shadow-xl shadow-indigo-200 hover:bg-indigo-700 hover:shadow-2xl transition-all transform active:scale-[0.98] text-xl flex items-center justify-center gap-3">
                    <span>حفظ وإرسال التقرير</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </div>
        @endif
            @if(Auth::user()->role == 'admin')
            <button wire:click="saveReplies" wire:loading.attr="disabled" class="flex items-center gap-2 px-6 py-3 font-bold text-white transition-transform bg-green-600 shadow-lg hover:bg-green-700 rounded-xl shadow-green-200 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="saveReplies">حفظ الردود</span>
                <span wire:loading wire:target="saveReplies">جاري الحفظ...</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            </button>
            @endif
    </div>
</div>


