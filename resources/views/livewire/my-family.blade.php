<div class="min-h-screen pb-24 bg-gray-50">

    <!-- Header -->
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center justify-center w-12 h-12 text-gray-700 transition-colors bg-gray-100 rounded-2xl hover:bg-gray-200" aria-label="الرئيسية">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-black leading-tight text-gray-900 md:text-3xl">عائلتي 👨‍👩‍👧‍👦</h1>
                        <p class="mt-1 text-sm font-medium text-gray-500">
                            {{ $family ? $family->name : 'لا توجد عائلة مرتبطة' }}
                        </p>
                    </div>
                </div>

                @if($family)
                    <button wire:click="toggleAddForm" class="flex items-center gap-2 px-5 py-3 font-bold text-white transition-all bg-indigo-600 shadow-md hover:bg-indigo-700 rounded-xl active:scale-95">
                        @if($showAddForm)
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            إغلاق النموذج
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            إضافة فرد جديد
                        @endif
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

        @if(!$family)
            <div class="py-20 text-center bg-white border-2 border-red-200 border-dashed rounded-3xl">
                <div class="flex items-center justify-center w-20 h-20 mx-auto mb-4 text-4xl rounded-full bg-red-50">⚠️</div>
                <h3 class="text-xl font-bold text-red-600">تنبيه هام</h3>
                <p class="mt-2 font-medium text-gray-600">حسابك غير مرتبط بأي عائلة حتى الآن.</p>
                <p class="mt-1 text-sm text-gray-400">يرجى مراجعة أمين الخدمة لربط حسابك.</p>
            </div>
        @else

            <!-- Add Member Form (Collapsible) -->
            <div x-show="$wire.showAddForm" x-collapse
                 class="relative p-6 mb-8 overflow-hidden bg-white border border-indigo-100 shadow-lg md:p-8 rounded-3xl animate-fade-in-down">
                <div class="absolute top-0 right-0 w-2 h-full bg-indigo-500"></div>

                <h3 class="flex items-center gap-2 mb-6 text-xl font-black text-gray-800">
                    <span class="p-2 text-indigo-600 bg-indigo-100 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                    </span>
                    بيانات المخدوم الجديد
                </h3>

                <form wire:submit="addMember" class="space-y-6">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <!-- Name -->
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">الاسم ثلاثي <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="new_member_name" placeholder="اكتب الاسم هنا..."
                                   class="w-full px-4 py-3 text-lg font-bold text-gray-900 placeholder-gray-400 transition-all border-2 border-gray-200 bg-gray-50 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                            @error('new_member_name') <p class="mt-1 text-sm font-bold text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">رقم التليفون</label>
                            <input type="tel" wire:model="new_member_phone" placeholder="01xxxxxxxxx"
                                   class="w-full px-4 py-3 text-lg font-bold text-right text-gray-900 placeholder-gray-400 transition-all border-2 border-gray-200 bg-gray-50 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 dir-ltr">
                        </div>

                        <!-- Birth Date -->
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">تاريخ الميلاد</label>
                            <input type="date" wire:model="new_birth_date"
                                   class="w-full px-4 py-3 text-lg font-bold text-gray-900 transition-all border-2 border-gray-200 bg-gray-50 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Job/College -->
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">الكلية / العمل</label>
                            <input type="text" wire:model="new_job_or_college" placeholder="مثال: هندسة / محاسب"
                                   class="w-full px-4 py-3 text-lg font-bold text-gray-900 placeholder-gray-400 transition-all border-2 border-gray-200 bg-gray-50 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Confession Father -->
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">أب الاعتراف</label>
                            <input type="text" wire:model="new_confession_father" placeholder="الاسم..."
                                   class="w-full px-4 py-3 text-lg font-bold text-gray-900 placeholder-gray-400 transition-all border-2 border-gray-200 bg-gray-50 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Talents -->
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">المواهب</label>
                            <input type="text" wire:model="new_talents" placeholder="رسم، ترنيم، تنظيم..."
                                   class="w-full px-4 py-3 text-lg font-bold text-gray-900 placeholder-gray-400 transition-all border-2 border-gray-200 bg-gray-50 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <button type="submit"
                                class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-black shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 w-full md:w-auto">
                            <span wire:loading.remove>حفظ وإضافة</span>
                            <span wire:loading>جاري الإضافة...</span>
                            <svg wire:loading.remove xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                    </div>
                </form>
            </div>

            @if (session()->has('message'))
                <div class="flex items-center gap-3 p-4 mb-8 font-bold text-green-800 bg-green-100 border-r-4 border-green-500 shadow-sm rounded-xl animate-fade-in-down">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ session('message') }}
                </div>
            @endif

            <!-- Members List -->
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <h3 class="text-xl font-black text-gray-800">أفراد الأسرة</h3>
                    <span class="px-3 py-1 text-sm font-bold text-indigo-700 bg-indigo-100 rounded-full">{{ $members->count() }}</span>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @forelse($members as $member)
                        <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md hover:border-indigo-300 transition-all duration-300 group relative {{ !$member->is_active ? 'opacity-75 bg-gray-50' : '' }}">

                            <div class="flex items-start gap-4">
                                <!-- Avatar link to stats -->
                                <a href="{{ route('member.stats', $member->id) }}" wire:navigate class="flex-shrink-0 block">
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center font-black text-2xl shadow-sm border-2 transition-colors {{ $member->is_active ? 'bg-indigo-50 text-indigo-600 border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white' : 'bg-gray-200 text-gray-400 border-gray-300' }}">
                                        {{ mb_substr($member->name, 0, 1) }}
                                    </div>
                                </a>

                                <div class="flex-grow pt-1">
                                    <!-- Name link to stats -->
                                    <a href="{{ route('member.stats', $member->id) }}" wire:navigate class="block">
                                        <h4 class="font-bold text-lg text-gray-900 group-hover:text-indigo-600 transition-colors {{ !$member->is_active ? 'line-through opacity-60' : '' }}">
                                            {{ $member->name }}
                                        </h4>
                                    </a>

                                    <div class="flex items-center gap-2 mt-1">
                                        @if($member->phone)
                                            <span class="text-sm font-medium text-gray-500 bg-gray-50 px-2 py-0.5 rounded" dir="ltr">{{ $member->phone }}</span>
                                        @else
                                            <span class="text-xs text-gray-400">لا يوجد رقم</span>
                                        @endif

                                        @if(!$member->is_active)
                                            <span class="text-xs font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded">غير نشط</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Toggle Active Action -->
                                <button wire:click="toggleActive({{ $member->id }})"
                                        class="w-10 h-10 flex items-center justify-center rounded-xl transition-colors {{ $member->is_active ? 'text-gray-300 hover:text-orange-500 hover:bg-orange-50' : 'text-green-500 hover:bg-green-50 bg-white border border-green-100 shadow-sm' }}"
                                        title="{{ $member->is_active ? 'نقل للأرشيف (تجميد)' : 'إعادة تفعيل' }}">
                                    @if($member->is_active)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="5" x="2" y="3" rx="1"/><path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                                    @endif
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="py-16 text-center bg-white border-2 border-gray-200 border-dashed col-span-full rounded-3xl">
                            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 text-4xl rounded-full bg-gray-50 grayscale opacity-30">👥</div>
                            <p class="text-lg font-bold text-gray-500">لا يوجد مخدومين حالياً</p>
                            <p class="mt-1 font-medium text-gray-400">ابدأ بإضافة أفراد أسرتك الآن!</p>
                            <button wire:click="toggleAddForm" class="mt-6 font-bold text-indigo-600 hover:underline">إضافة فرد جديد</button>
                        </div>
                    @endforelse
                </div>
            </div>

        @endif
    </div>
</div>
