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
                        <h1 class="text-xl font-black leading-tight text-gray-900 md:text-2xl">
                            {{ $isReadOnly ? 'مراجعة التقرير الأسبوعي' : 'إنشاء تقرير أسبوعي' }}
                        </h1>
                        <p class="mt-1 text-sm font-bold text-gray-500">{{ $family->name ?? '' }}</p>
                    </div>
                </div>

                @if($isReadOnly)
                    <div class="flex gap-3">
                        <button wire:click="saveReplies" wire:loading.attr="disabled" class="flex items-center gap-2 px-6 py-3 font-bold text-white transition-transform bg-green-600 shadow-lg hover:bg-green-700 rounded-xl shadow-green-200 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="saveReplies">حفظ الردود</span>
                            <span wire:loading wire:target="saveReplies">جاري الحفظ...</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        </button>
                        @if(Auth::user()->role == 'admin')
                            <button wire:click="closeReplies" class="flex items-center gap-2 px-6 py-3 font-bold text-white transition-transform bg-blue-600 shadow-lg hover:bg-blue-700 rounded-xl shadow-blue-50 active:scale-95">
                                <span wire:loading.remove wire:target="closeReplies">إغلاق التقرير</span>
                                <span wire:loading wire:target="closeReplies">جاري الإغلاق...</span>
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

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Timeline (ترتيب لقاء التلمذة) -->
            <div class="p-2 bg-white border border-gray-200 shadow-sm lg:col-span-2 rounded-3xl">
                <h3 class="flex items-center gap-2 pb-3 mb-6 text-lg font-black text-gray-800 border-b border-gray-100">
                    <span class="p-2 text-indigo-600 bg-indigo-100 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                    ترتيب لقاء التلمذة
                </h3>
                <div class="space-y-4">
                    @foreach($timeline as $index => $row)
                        <div class="flex flex-col gap-3  border-2 border-gray-200 group bg-gray-50 rounded-2xl transition-colors {{ $isReadOnly ? 'border-indigo-100 bg-indigo-50/50 hover:border-indigo-200' : 'hover:border-indigo-300' }}">
                            <div class="flex items-center gap-3">
                                @if(!$isReadOnly)
                                    <input type="text" wire:model="timeline.{{ $index }}.time" placeholder="6-6:30" class="w-24 py-4 font-mono text-sm font-bold text-center border-2 border-gray-200 bg-white rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    <input type="text" wire:model="timeline.{{ $index }}.activity" placeholder="اكتب النشاط هنا..." class="flex-grow py-4 text-base font-bold border-2 border-gray-200 bg-white rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    <button wire:click="removeTimelineRow({{ $index }})" class="flex items-center justify-center w-12 h-12 text-gray-400 transition-colors bg-white border border-gray-200 rounded-xl hover:text-red-600 hover:bg-red-50 hover:border-red-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    </button>
                                @else
                                    <div class="flex items-center w-full gap-4">
                                        <span class="px-4 py-2 font-mono text-sm font-black text-indigo-700 bg-white border border-indigo-100 rounded-xl shadow-sm">
                                            {{ isset($row['time']) ? $row['time'] : (($row['start'] ?? '') . ' - ' . ($row['end'] ?? '')) }}
                                        </span>
                                        <span class="text-base font-bold text-gray-900">{{ $row['activity'] }}</span>
                                    </div>
                                @endif
                            </div>

                            @if($isReadOnly)
                                @foreach($row['replies'] ?? [] as $reply)
                                    <div class="flex items-start gap-3 p-4 mt-3 rounded-xl border {{ $reply['role'] == 'admin' ? 'bg-indigo-100 border-indigo-200' : 'bg-white border-gray-200 shadow-sm' }}">
                                        <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-full {{ $reply['role'] == 'admin' ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-600' }}">
                                            {{ $reply['role'] == 'admin' ? '👨‍💼' : '👤' }}
                                        </div>
                                        <div>
                                            <span class="block mb-1 text-xs font-black tracking-wider uppercase {{ $reply['role'] == 'admin' ? 'text-indigo-800' : 'text-gray-500' }}">{{ $reply['name'] }}</span>
                                            <p class="text-sm font-bold leading-relaxed {{ $reply['role'] == 'admin' ? 'text-indigo-900' : 'text-gray-800' }}">{{ $reply['text'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="flex items-start gap-3 pt-3 mt-2 border-t border-indigo-200/50">
                                    <div class="px-3 py-1.5 mt-1 text-xs font-black text-indigo-800 bg-indigo-200 rounded-lg shrink-0">رد جديد:</div>
                                    <textarea wire:model="timeline.{{ $index }}.new_reply" rows="2" class="flex-grow p-3 text-sm font-bold border-2 border-indigo-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 bg-white" placeholder="اكتب ردك هنا..."></textarea>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                @if(!$isReadOnly)
                    <button wire:click="addTimelineRow" class="flex items-center justify-center w-full gap-2 py-4 mt-6 text-sm font-black text-indigo-600 transition-all border-2 border-indigo-200 border-dashed rounded-2xl hover:bg-indigo-50 hover:border-indigo-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        إضافة فقرة جديدة
                    </button>
                @endif
            </div>

            <!-- Visitation & Session Details (Col Span 1) -->
            <div class="flex flex-col gap-6 lg:col-span-1">

                <!-- Card 1: ساعات الافتقاد -->
                <div class="flex flex-col p-6 bg-white border border-gray-200 shadow-sm rounded-3xl">
                    <div class="flex flex-col items-center justify-center {{ $isReadOnly ? 'mb-4' : 'h-full' }}">
                        <div class="flex items-center justify-center w-16 h-16 mb-4 text-3xl text-blue-600 border border-blue-100 shadow-sm bg-blue-50 rounded-2xl">🏃‍♂️</div>
                        <h3 class="mb-4 text-lg font-black text-gray-800">ساعات الافتقاد</h3>
                        @if(!$isReadOnly)
                            <div class="flex items-center justify-center gap-2">
                                <input type="number" step="0.5" min="0" wire:model="visitation_hours" class="w-24 text-3xl font-black text-center text-blue-700 border-2 border-gray-200 h-14 bg-gray-50 rounded-2xl focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="0">
                                <span class="text-sm font-bold text-gray-500">ساعة</span>
                            </div>
                            @error('visitation_hours') <span class="block mt-2 text-sm font-bold text-red-500">{{ $message }}</span> @enderror
                        @else
                            <div class="w-full px-6 py-4 text-center border border-blue-100 bg-blue-50 rounded-2xl">
                                <span class="block text-4xl font-black text-blue-700">{{ $visitation_hours ?? 0 }}</span>
                                <span class="text-xs font-bold tracking-wider text-blue-500 uppercase mt-1 block">ساعة هذا الأسبوع</span>
                            </div>
                        @endif
                    </div>
                    @if($isReadOnly)
                        <div class="flex-grow w-full mt-2 space-y-3">
                            @foreach($visitation_replies ?? [] as $reply)
                                <div class="flex items-start gap-3 p-4 rounded-xl border shadow-sm {{ $reply['role'] == 'admin' ? 'bg-blue-100 border-blue-200' : 'bg-white border-gray-200' }}">
                                    <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-full {{ $reply['role'] == 'admin' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-600' }}">
                                        {{ $reply['role'] == 'admin' ? '👨‍💼' : '👤' }}
                                    </div>
                                    <div>
                                        <span class="block mb-1 text-xs font-black tracking-wider uppercase {{ $reply['role'] == 'admin' ? 'text-blue-800' : 'text-gray-500' }}">{{ $reply['name'] }}</span>
                                        <p class="text-sm font-bold leading-relaxed {{ $reply['role'] == 'admin' ? 'text-blue-900' : 'text-gray-800' }}">{{ $reply['text'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                            <div class="flex items-start gap-2 pt-3 mt-4 border-t border-blue-100">
                                <span class="text-[10px] bg-blue-100 text-blue-800 font-black px-2 py-1.5 rounded-lg shrink-0 mt-1">رد:</span>
                                <textarea wire:model="visitation_new_reply" rows="2" class="flex-grow p-3 text-xs font-bold border-2 border-blue-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 bg-blue-50/50" placeholder="تعليق..."></textarea>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Card 2: معاد الجلسة -->
                <div class="flex flex-col p-6 bg-white border border-gray-200 shadow-sm rounded-3xl">
                    <div class="flex flex-col items-center justify-center {{ $isReadOnly ? 'mb-4' : 'h-full' }}">
                        <div class="flex items-center justify-center w-16 h-16 mb-4 text-3xl text-purple-600 border border-purple-100 shadow-sm bg-purple-50 rounded-2xl">🕒</div>
                        <h3 class="mb-4 text-lg font-black text-gray-800">معاد الجلسة</h3>
                        @if(!$isReadOnly)
                            <div class="w-full">
                                <input type="text" wire:model="session_time" class="w-full text-base font-bold text-center text-purple-700 border-2 border-gray-200 py-3 bg-gray-50 rounded-2xl focus:ring-purple-500 focus:border-purple-500 transition-colors" placeholder="مثال: الثلاثاء 7 مساءً">
                            </div>
                            @error('session_time') <span class="block mt-2 text-sm font-bold text-red-500">{{ $message }}</span> @enderror
                        @else
                            <div class="w-full px-6 py-4 text-center border border-purple-100 bg-purple-50 rounded-2xl">
                                <span class="block text-xl font-black text-purple-700">{{ $session_time ?: 'لم يحدد' }}</span>
                                <span class="text-xs font-bold tracking-wider text-purple-500 uppercase mt-1 block">توقيت الجلسة</span>
                            </div>
                        @endif
                    </div>
                    @if($isReadOnly)
                        <div class="flex-grow w-full mt-2 space-y-3">
                            @foreach($session_replies ?? [] as $reply)
                                <div class="flex items-start gap-3 p-4 rounded-xl border shadow-sm {{ $reply['role'] == 'admin' ? 'bg-purple-100 border-purple-200' : 'bg-white border-gray-200' }}">
                                    <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-full {{ $reply['role'] == 'admin' ? 'bg-purple-600 text-white' : 'bg-purple-100 text-purple-600' }}">
                                        {{ $reply['role'] == 'admin' ? '👨‍💼' : '👤' }}
                                    </div>
                                    <div>
                                        <span class="block mb-1 text-xs font-black tracking-wider uppercase {{ $reply['role'] == 'admin' ? 'text-purple-800' : 'text-gray-500' }}">{{ $reply['name'] }}</span>
                                        <p class="text-sm font-bold leading-relaxed {{ $reply['role'] == 'admin' ? 'text-purple-900' : 'text-gray-800' }}">{{ $reply['text'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                            <div class="flex items-start gap-2 pt-3 mt-4 border-t border-purple-100">
                                <span class="text-[10px] bg-purple-100 text-purple-800 font-black px-2 py-1.5 rounded-lg shrink-0 mt-1">رد:</span>
                                <textarea wire:model="session_new_reply" rows="2" class="flex-grow p-3 text-xs font-bold border-2 border-purple-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 bg-purple-50/50" placeholder="تعليق..."></textarea>
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        <!-- Achievements -->
        <div class="p-6 md:p-8 bg-white border border-gray-200 shadow-sm rounded-3xl">
            <h3 class="flex items-center gap-2 pb-4 mb-6 text-xl font-black text-gray-800 border-b border-gray-100">
                <span class="p-2 text-green-600 bg-green-100 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></span>
                ما تم تنفيذه وأفكار جديدة
            </h3>
            <div class="space-y-6">
                @foreach($weekly_achievements as $index => $item)
                    <div class="p-6 border-2 border-gray-100 group bg-gray-50 rounded-2xl transition-colors hover:border-green-200">
                        @if(!$isReadOnly)
                            <div class="flex items-start gap-4">
                                <div class="mt-3 text-2xl font-black text-green-500">•</div>
                                <div class="w-full">
                                    <textarea wire:model="weekly_achievements.{{ $index }}.text" rows="3" class="w-full p-5 text-base font-bold leading-relaxed placeholder-gray-400 bg-white border-2 border-gray-200 rounded-2xl focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="اكتب نقطة محددة..."></textarea>
                                    <div class="flex justify-end mt-3">
                                        <button wire:click="removeItem('weekly_achievements', {{ $index }})" class="flex items-center gap-1 px-4 py-2 text-xs font-bold text-red-600 transition-colors bg-white border border-gray-200 rounded-lg hover:bg-red-50 hover:border-red-200 shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> حذف
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
                            <div class="mt-4 space-y-3">
                                @foreach($item['replies'] ?? [] as $reply)
                                    <div class="flex items-start gap-3 p-4 rounded-xl border shadow-sm {{ $reply['role'] == 'admin' ? 'bg-green-100 border-green-200' : 'bg-white border-gray-200' }}">
                                        <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-full {{ $reply['role'] == 'admin' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-600' }}">
                                            {{ $reply['role'] == 'admin' ? '👨‍💼' : '👤' }}
                                        </div>
                                        <div>
                                            <span class="block mb-1 text-xs font-black tracking-wider uppercase {{ $reply['role'] == 'admin' ? 'text-green-800' : 'text-gray-500' }}">{{ $reply['name'] }}</span>
                                            <p class="text-sm font-bold leading-relaxed {{ $reply['role'] == 'admin' ? 'text-green-900' : 'text-gray-800' }}">{{ $reply['text'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="flex items-start gap-3 pt-4 mt-2 border-t border-green-200/50">
                                    <div class="px-3 py-1.5 mt-1 text-xs font-black text-green-800 bg-green-200 rounded-lg shrink-0">رد جديد:</div>
                                    <textarea wire:model="weekly_achievements.{{ $index }}.new_reply" rows="2" class="flex-grow p-3 text-sm font-bold border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-green-500 bg-white" placeholder="اكتب ردك هنا..."></textarea>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            @if(!$isReadOnly)
                <button wire:click="addItem('weekly_achievements')" class="flex items-center justify-center w-full gap-2 py-4 mt-6 text-base font-black text-green-700 transition-all border-2 border-green-200 border-dashed bg-green-50 hover:bg-green-100 rounded-2xl">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    إضافة نقطة أخرى
                </button>
            @endif
        </div>

        <!-- Priest Messages -->
        <div class="p-6 md:p-8 bg-white border-2 border-blue-200 shadow-sm rounded-3xl">
            <h3 class="flex items-center gap-2 pb-4 mb-6 text-xl font-black text-gray-800 border-b border-gray-100">
                <span class="p-2 text-white bg-blue-500 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
                رسائل للأب الكاهن / الأمين
            </h3>
            <div class="space-y-6">
                @foreach($priest_message as $index => $item)
                    <div class="p-6 border-2 border-blue-100 group bg-blue-50/50 rounded-2xl transition-colors hover:border-blue-300">
                        @if(!$isReadOnly)
                            <div class="flex items-start gap-4">
                                <div class="w-full">
                                    <textarea wire:model="priest_message.{{ $index }}.text" rows="3" class="w-full p-5 text-base font-bold leading-relaxed placeholder-blue-300 bg-white border-2 border-blue-200 rounded-2xl focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="اكتب رسالتك أو استفسارك..."></textarea>
                                    <div class="flex justify-end mt-3">
                                        <button wire:click="removeItem('priest_message', {{ $index }})" class="flex items-center gap-1 px-4 py-2 text-xs font-bold text-red-600 transition-colors bg-white border border-gray-200 rounded-lg hover:bg-red-50 hover:border-red-200 shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> حذف
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex items-start gap-3">
                                <div class="mt-1 text-2xl font-black leading-none text-blue-500">✉️</div>
                                <p class="text-base font-bold leading-relaxed text-gray-900">{{ $item['text'] }}</p>
                            </div>
                        @endif

                        @if($isReadOnly)
                            <div class="mt-4 space-y-3">
                                @foreach($item['replies'] ?? [] as $reply)
                                    <div class="flex items-start gap-3 p-4 rounded-xl border shadow-sm {{ $reply['role'] == 'admin' ? 'bg-blue-100 border-blue-200' : 'bg-white border-gray-200' }}">
                                        <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-full {{ $reply['role'] == 'admin' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-600' }}">
                                            {{ $reply['role'] == 'admin' ? '👨‍💼' : '👤' }}
                                        </div>
                                        <div>
                                            <span class="block mb-1 text-xs font-black tracking-wider uppercase {{ $reply['role'] == 'admin' ? 'text-blue-800' : 'text-gray-500' }}">{{ $reply['name'] }}</span>
                                            <p class="text-sm font-bold leading-relaxed {{ $reply['role'] == 'admin' ? 'text-blue-900' : 'text-gray-800' }}">{{ $reply['text'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="flex items-start gap-3 pt-4 mt-2 border-t border-blue-200/50">
                                    <div class="px-3 py-1.5 mt-1 text-xs font-black text-blue-800 bg-blue-200 rounded-lg shrink-0">رد جديد:</div>
                                    <textarea wire:model="priest_message.{{ $index }}.new_reply" rows="2" class="flex-grow p-3 text-sm font-bold border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-blue-500 bg-white" placeholder="اكتب ردك هنا..."></textarea>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            @if(!$isReadOnly)
                <button wire:click="addItem('priest_message')" class="flex items-center justify-center w-full gap-2 py-4 mt-6 text-base font-black text-blue-700 transition-all border-2 border-blue-200 border-dashed bg-blue-50 hover:bg-blue-100 rounded-2xl">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    إضافة رسالة جديدة
                </button>
            @endif
        </div>

        @if(!$isReadOnly)
            <div class="pt-6 pb-8">
                <button wire:click="save" class="flex items-center justify-center w-full gap-3 py-5 text-xl font-black text-white transition-all transform shadow-xl bg-indigo-600 rounded-2xl shadow-indigo-200 hover:bg-indigo-700 hover:shadow-2xl active:scale-[0.98]">
                    <span>حفظ وإرسال التقرير</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </div>
        @endif
    </div>
</div>
