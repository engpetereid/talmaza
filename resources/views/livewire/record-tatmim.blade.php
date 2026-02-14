<div class="min-h-screen pb-32 bg-gray-50">

    <!-- Header -->
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20"> <!-- Increased height for touch targets -->
                <div class="flex items-center gap-4">
                    @if($step == 2)
                        <button wire:click="$set('step', 1)" class="flex items-center justify-center w-12 h-12 text-gray-700 transition-colors bg-gray-100 rounded-full hover:bg-gray-200" aria-label="عودة">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                        </button>
                    @else
                        <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center justify-center w-12 h-12 text-gray-700 transition-colors bg-gray-100 rounded-full hover:bg-gray-200" aria-label="الرئيسية">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                        </a>
                    @endif

                    <div>
                        <h1 class="text-xl font-black text-gray-900 md:text-2xl">
                            {{ $step == 1 ? 'تفاصيل الاجتماع' : 'قائمة الحضور' }}
                        </h1>
                        <p class="mt-1 text-sm font-bold text-gray-500 md:text-base">الخطوة {{ $step }} من 2</p>
                    </div>
                </div>

                <div wire:loading class="px-4 py-2 text-sm font-bold text-indigo-700 rounded-lg animate-pulse bg-indigo-50">
                    جاري المعالجة...
                </div>
            </div>
        </div>
    </div>

    @if($readonly)
        <div class="p-4 text-center border-b border-yellow-200 bg-yellow-50">
            <p class="text-base font-bold text-yellow-900">⚠️ وضع العرض فقط: لا يمكن تعديل البيانات</p>
        </div>
    @endif

    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

        <!-- STEP 1: Meeting Details -->
        @if($step == 1)
            <div class="max-w-3xl mx-auto space-y-8 animate-fade-in-up">

                <!-- Status Selection -->
                <section>
                    <label class="block mb-4 text-xl font-black text-gray-800">حالة الاجتماع هذا الأسبوع</label>
                    <div class="grid grid-cols-2 gap-6">
                        <button wire:click="$set('status', 'completed')"
                                @if($readonly) disabled @endif
                                class="relative overflow-hidden rounded-3xl p-6 border-4 transition-all duration-200 flex flex-col items-center justify-center gap-3 h-48 {{ $status == 'completed' ? 'border-green-500 bg-green-50 text-green-900 shadow-md' : 'border-gray-200 bg-white text-gray-500 hover:border-gray-300' }}">
                            @if($status == 'completed') <div class="absolute p-1 text-white bg-green-500 rounded-full top-4 left-4"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div> @endif
                            <span class="text-6xl">🙌</span>
                            <span class="text-xl font-black">تم الاجتماع</span>
                        </button>

                        <button wire:click="$set('status', 'cancelled')"
                                @if($readonly) disabled @endif
                                class="relative overflow-hidden rounded-3xl p-6 border-4 transition-all duration-200 flex flex-col items-center justify-center gap-3 h-48 {{ $status == 'cancelled' ? 'border-red-500 bg-red-50 text-red-900 shadow-md' : 'border-gray-200 bg-white text-gray-500 hover:border-gray-300' }}">
                            @if($status == 'cancelled') <div class="absolute p-1 text-white bg-red-500 rounded-full top-4 left-4"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div> @endif
                            <span class="text-6xl">🚫</span>
                            <span class="text-xl font-black">لم يتم / إلغاء</span>
                        </button>
                    </div>
                </section>

                @if($status == 'completed')
                    <!-- Curriculum Details -->
                    <section class="p-6 space-y-6 bg-white border border-gray-200 shadow-sm rounded-3xl">
                        <h2 class="pb-2 text-xl font-black text-gray-800 border-b border-gray-100">تفاصيل الدرس</h2>

                        @if($suggestedLessonText && !$readonly)
                            <div class="flex items-start gap-3 p-5 text-base font-bold text-indigo-900 bg-indigo-50 rounded-2xl">
                                <span class="text-2xl">💡</span>
                                <p class="leading-relaxed">{{ $suggestedLessonText }}</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="block mb-3 text-base font-bold text-gray-700">المرحلة</label>
                                <select wire:model.live="selected_stage_id" @if($readonly) disabled @endif class="w-full px-4 py-4 text-lg font-bold text-gray-900 border-2 border-gray-300 bg-gray-50 rounded-2xl focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">اختر المرحلة...</option>
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($selected_stage_id)
                                <div>
                                    <label class="block mb-3 text-base font-bold text-gray-700">الدرس</label>
                                    <select wire:model.live="selected_lesson_id" @if($readonly) disabled @endif class="w-full px-4 py-4 text-lg font-bold text-indigo-900 border-2 border-indigo-300 bg-indigo-50 rounded-2xl focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">اختر الدرس...</option>
                                        @foreach($stages->find($selected_stage_id)->lessons as $lesson)
                                            <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        <div class="relative flex items-center py-4">
                            <div class="flex-grow border-t-2 border-gray-200"></div>
                            <span class="flex-shrink-0 mx-4 text-base font-bold text-gray-400">أو موضوع خارجي</span>
                            <div class="flex-grow border-t-2 border-gray-200"></div>
                        </div>

                        <input type="text" wire:model="custom_topic" @if($readonly) disabled @endif placeholder="اكتب عنوان الموضوع الحر هنا..." class="w-full px-5 py-4 text-lg font-bold text-gray-900 border-2 border-gray-300 bg-gray-50 rounded-2xl focus:ring-indigo-500 focus:border-indigo-500">
                    </section>

                    <!-- Score Config -->
                    <section class="p-8 text-center text-white shadow-lg bg-gradient-to-r from-indigo-600 to-purple-700 rounded-3xl">
                        <label class="block mb-4 text-lg font-bold text-indigo-100">درجة النوتة الروحية</label>
                        <div class="flex items-center justify-center gap-8">
                            @if(!$readonly)
                                <button wire:click="$set('max_note_score', {{ (int)$max_note_score - 1 }})" class="flex items-center justify-center text-3xl font-bold transition-colors rounded-full w-14 h-14 bg-white/20 hover:bg-white/30">-</button>
                            @endif
                            <span class="font-black tracking-tighter text-7xl">{{ $max_note_score }}</span>
                            @if(!$readonly)
                                <button wire:click="$set('max_note_score', {{ (int)$max_note_score + 1 }})" class="flex items-center justify-center text-3xl font-bold transition-colors rounded-full w-14 h-14 bg-white/20 hover:bg-white/30">+</button>
                            @endif
                        </div>
                    </section>
                @endif
            </div>
        @endif

        <!-- STEP 2: Members List -->
        @if($step == 2)
            <!-- Responsive Grid: 1 col mobile, 2 cols tablet, 3 cols PC -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($members as $member)
                    <!-- Member Card -->
                    <div class="flex flex-col h-full overflow-hidden transition-all duration-300 bg-white border-2 border-gray-200 shadow-sm rounded-3xl hover:border-indigo-300">

                        <!-- Card Header -->
                        <div class="flex items-center gap-4 p-5 bg-white border-b border-gray-100">
                            <div class="flex items-center justify-center text-xl font-black text-indigo-700 border-2 border-indigo-100 rounded-full w-14 h-14 bg-indigo-50">
                                {{ mb_substr($member->name, 0, 1) }}
                            </div>
                            <h3 class="text-xl font-black text-gray-900">{{ $member->name }}</h3>
                        </div>

                        <!-- Card Body -->
                        <div class="flex-grow p-5 space-y-6 bg-gray-50/50">

                            <!-- Scores Section -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-center text-gray-600">النوتة ({{ $max_note_score }})</label>
                                    <input type="number"
                                           inputmode="numeric"
                                           min="0" max="{{ $max_note_score }}"
                                           @if($readonly) disabled @endif
                                           wire:model.blur="records.{{ $member->id }}.note_score"
                                           class="block w-full p-4 text-2xl font-black text-center text-gray-900 bg-white border-2 border-gray-300 rounded-2xl focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-center text-gray-600">تدريب التلمذة(7)</label>
                                    <input type="number"
                                           inputmode="numeric"
                                           min="0" max="7"
                                           @if($readonly) disabled @endif
                                           wire:model.blur="records.{{ $member->id }}.talmaza_training_count"
                                           class="block w-full p-4 text-2xl font-black text-center text-gray-900 bg-white border-2 border-gray-300 rounded-2xl focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-center text-gray-600">مشاركة الخلوة(7)</label>
                                    <input type="number"
                                           inputmode="numeric"
                                           min="0" max="7"
                                           @if($readonly) disabled @endif
                                           wire:model.blur="records.{{ $member->id }}.kholwa_count"
                                           class="block w-full p-4 text-2xl font-black text-center text-gray-900 bg-white border-2 border-gray-300 rounded-2xl focus:ring-orange-500 focus:border-orange-500">
                                </div>
                            </div>

                            <!-- Toggles Grid -->
                            <div class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                                @php
                                    $toggles = [
                                        'is_present' => ['label' => 'حضور', 'icon' => '🙋‍♂️'],
                                        'has_mass' => ['label' => 'القداس', 'icon' => '⛪'],
                                        'has_tasbeha' => ['label' => 'تسبحة', 'icon' => '🕯️'],
                                        'has_weekly_kholwa' => ['label' => 'خلوة اسبوعية', 'icon' => '🧘‍♂️'],
                                        'has_servants_meeting' => ['label' => 'اجتماع الخدام', 'icon' => '🤝'],
                                        'has_reading' => ['label' => 'قراءة', 'icon' => '📖'],
                                        'has_family_altar' => ['label' => 'مذبح', 'icon' => '👨‍👩‍👧‍👦'],
                                    ];
                                @endphp

                                @foreach($toggles as $field => $data)
                                    <button
                                        @if($readonly) disabled @endif
                                        wire:click="updateRecord({{ $member->id }}, '{{ $field }}', {{ ($records[$member->id][$field] ?? false) ? 0 : 1 }})"
                                        class="flex flex-col items-center justify-center p-2 transition-all border-2 h-28 rounded-2xl
                                        {{ ($records[$member->id][$field] ?? false)
                                            ? 'bg-indigo-100 border-indigo-500 text-indigo-900 shadow-inner'
                                            : 'bg-white border-gray-200 text-gray-400 hover:border-gray-400 hover:bg-gray-50' }}">
                                        <span class="mb-2 text-3xl filter {{ ($records[$member->id][$field] ?? false) ? '' : 'grayscale opacity-60' }}">{{ $data['icon'] }}</span>
                                        <span class="text-xs font-black leading-tight">{{ $data['label'] }}</span>
                                    </button>
                                @endforeach
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

    <!-- Floating Action Button (Footer) -->
    <div class="fixed bottom-0 left-0 w-full p-4 bg-white/95 backdrop-blur-md border-t border-gray-200 shadow-[0_-4px_20px_rgba(0,0,0,0.1)] z-[60]">
        <div class="max-w-2xl mx-auto">
            @if($step == 1)
                @if($status == 'completed')
                    <!-- ENABLED NEXT BUTTON IN READONLY MODE -->
                    <button wire:click="saveMeetingDetails" class="flex items-center justify-center w-full gap-3 py-4 text-xl font-bold text-white transition-transform bg-indigo-600 shadow-lg hover:bg-indigo-700 rounded-2xl shadow-indigo-200 active:scale-[0.98]">
                        <span>{{ $readonly ? 'عرض القائمة' : 'التالي: قائمة المخدومين' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 5 7 7-7 7"/><path d="M5 12h14"/></svg>
                    </button>
                @else
                    <button wire:click="saveMeetingDetails" @if($readonly) disabled @endif class="flex items-center justify-center w-full gap-3 py-4 text-xl font-bold text-white transition-transform bg-red-600 shadow-lg hover:bg-red-700 rounded-2xl shadow-red-200 active:scale-[0.98]">
                        <span>تأكيد الإلغاء وحفظ</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </button>
                @endif
            @else
                <button wire:click="finish" class="flex items-center justify-center w-full gap-3 py-4 text-xl font-bold text-white transition-transform shadow-lg rounded-2xl active:scale-[0.98] {{ $readonly ? 'bg-gray-600 hover:bg-gray-700 shadow-gray-200' : 'bg-green-600 hover:bg-green-700 shadow-green-200' }}">
                    <span>{{ $readonly ? 'عودة للوحة التحكم' : 'إنهاء وحفظ التتميم' }}</span>
                    @if($readonly)
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    @endif
                </button>
            @endif
        </div>
    </div>
</div>
