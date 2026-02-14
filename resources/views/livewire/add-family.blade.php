<div class="min-h-screen pb-20 bg-gray-50">

    <!-- Header -->
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center h-20 gap-4">
                <a href="{{ url()->previous() }}" wire:navigate
                    class="flex items-center justify-center w-12 h-12 text-gray-700 transition-colors bg-gray-100 rounded-full hover:bg-gray-200"
                    aria-label="عودة للوحة التحكم">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                </a>
                <h1 class="text-2xl font-black text-gray-900">إضافة عائلة جديدة</h1>
            </div>
        </div>
    </div>

    <div class="max-w-5xl px-4 py-8 mx-auto sm:px-6 lg:px-8">

        <!-- Responsive Grid: 1 Column on Mobile, 2 Columns on PC -->
        <div class="grid items-start grid-cols-1 gap-8 md:grid-cols-2">

            <!-- 1. Family Data Card -->
            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-3xl md:p-8 animate-fade-in-up">
                <div class="flex items-center gap-3 pb-4 mb-6 border-b border-gray-100">
                    <div class="flex items-center justify-center w-12 h-12 text-indigo-600 bg-indigo-100 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z" />
                            <path
                                d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">بيانات العائلة</h3>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block mb-2 text-base font-bold text-gray-800">اسم العائلة (الشفيع)</label>
                        <div class="relative">
                            <input type="text" wire:model="family_name" placeholder="مثال: عائلة الأنبا بيشوي"
                                class="w-full px-5 py-4 text-lg text-gray-900 placeholder-gray-400 transition-all border-2 border-gray-200 bg-gray-50 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                        @error('family_name') <p class="flex items-center gap-1 mt-2 text-sm font-bold text-red-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>{{ $message }}
                        </p> @enderror
                    </div>


                </div>
            </div>

            <!-- 2. Leader Data Card -->
            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-3xl md:p-8 animate-fade-in-up"
                style="animation-delay: 0.1s;">
                <div class="flex items-center gap-3 pb-4 mb-6 border-b border-gray-100">
                    <div class="flex items-center justify-center w-12 h-12 text-orange-600 bg-orange-100 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">بيانات الخادم المسئول (القائد)</h3>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block mb-2 text-base font-bold text-gray-800">اسم الخادم</label>
                        <div class="relative">
                            <input type="text" wire:model="leader_name" placeholder="الاسم ثلاثي"
                                class="w-full px-5 py-4 text-lg text-gray-900 placeholder-gray-400 transition-all border-2 border-gray-200 bg-gray-50 rounded-xl focus:ring-orange-500 focus:border-orange-500">
                        </div>
                        @error('leader_name') <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-base font-bold text-gray-800">رقم التليفون (للدخول)</label>
                        <div class="relative">
                            <input type="tel" wire:model="leader_phone" placeholder="01xxxxxxxxx"
                                class="w-full px-5 py-4 text-lg text-right text-gray-900 placeholder-gray-400 transition-all border-2 border-gray-200 bg-gray-50 rounded-xl focus:ring-orange-500 focus:border-orange-500 dir-ltr">
                            <div
                                class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                </svg>
                            </div>
                        </div>
                        @error('leader_phone') <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-base font-bold text-gray-800">كلمة المرور</label>
                        <div class="relative">
                            <input type="text" wire:model="leader_password" placeholder="كلمة سر مبدئية"
                                class="w-full px-5 py-4 text-lg text-gray-900 placeholder-gray-400 transition-all border-2 border-gray-200 bg-gray-50 rounded-xl focus:ring-orange-500 focus:border-orange-500">
                            <div
                                class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </div>
                        </div>
                        @error('leader_password') <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

        </div>

        <!-- Submit Button -->
        <div class="max-w-5xl mx-auto mt-8">
            <button wire:click="save"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-5 rounded-2xl font-black shadow-xl shadow-indigo-200 flex items-center justify-center gap-3 text-xl transition-transform active:scale-[0.98] group">
                <span wire:loading.remove>حفظ وإنشاء العائلة</span>
                <span wire:loading>جاري الحفظ...</span>
                <svg wire:loading.remove xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                    class="transition-transform group-hover:translate-x-1">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
            </button>
        </div>

    </div>
</div>