<div class="min-h-screen pb-24 bg-gray-50">

    <!-- Header -->
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ Auth::user()->role == 'admin' ? route('admin.dashboard') : route('dashboard') }}"
                        wire:navigate
                        class="flex items-center justify-center w-12 h-12 text-gray-700 transition-colors bg-gray-100 rounded-2xl hover:bg-gray-200"
                        aria-label="عودة">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                    </a>
                    <h1 class="text-2xl font-black text-gray-900 md:text-3xl">الملف الشخصي 👤</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 py-8 mx-auto space-y-8 max-w-7xl sm:px-6 lg:px-8">

        <!-- Grid Layout: 1 Column Mobile, 2 Columns PC -->
        <div class="grid items-start grid-cols-1 gap-8 lg:grid-cols-2">

            <!-- COLUMN 1: Personal Info -->
            <div class="space-y-8">
                <!-- Profile Card -->
                <div class="bg-white p-6 md:p-8 rounded-[2rem] shadow-sm border border-gray-200">
                    <div class="flex items-center gap-6 pb-6 mb-8 border-b border-gray-100">
                        <div
                            class="flex items-center justify-center w-24 h-24 text-4xl font-black text-indigo-600 border-4 border-white rounded-full shadow-lg bg-indigo-50">
                            {{ mb_substr($name, 0, 1) }}
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-900">{{ Auth::user()->name }}</h2>
                            <p class="mt-1 text-base font-bold text-gray-500">
                                {{ Auth::user()->role == 'admin' ? 'مسئول النظام' : 'خادم قائد' }}
                            </p>
                            @if(Auth::user()->family)
                                <span
                                    class="inline-block mt-3 px-4 py-1.5 bg-green-50 text-green-700 text-sm rounded-xl font-bold border border-green-100">
                                    {{ Auth::user()->family->name }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <form wire:submit="updateProfileInformation" class="space-y-6">
                        <div>
                            <label class="block mb-2 text-base font-bold text-gray-700">الاسم ثلاثي</label>
                            <input type="text" wire:model="name"
                                class="w-full px-5 py-4 text-lg font-bold placeholder-gray-400 transition-all border-2 border-gray-200 bg-gray-50 rounded-2xl focus:ring-indigo-500 focus:border-indigo-500">
                            @error('name') <span
                                class="flex items-center block gap-1 mt-1 text-sm font-bold text-red-600"><svg
                                    class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block mb-2 text-base font-bold text-gray-700">رقم التليفون</label>
                            <input type="tel" wire:model="phone" readonly disabled
                                class="w-full px-5 py-4 text-lg font-bold text-right placeholder-gray-400 transition-all border-2 border-gray-200 bg-gray-50 rounded-2xl dir-ltr ">
                            @error('phone') <span
                                class="flex items-center block gap-1 mt-1 text-sm font-bold text-red-600"><svg
                                    class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col items-center justify-between gap-4 pt-4 sm:flex-row">
                            <button type="submit"
                                class="flex items-center justify-center w-full gap-2 px-8 py-4 text-lg font-black text-white transition-all bg-indigo-600 shadow-lg sm:w-auto rounded-xl hover:bg-indigo-700 active:scale-95">
                                <span wire:loading.remove wire:target="updateProfileInformation">حفظ التعديلات</span>
                                <span wire:loading wire:target="updateProfileInformation">جاري الحفظ...</span>
                                <svg wire:loading.remove xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </button>

                            @if (session()->has('profile-status'))
                                <span
                                    class="flex items-center justify-center w-full gap-2 px-4 py-3 text-base font-bold text-green-700 bg-green-100 border border-green-200 sm:w-auto rounded-xl animate-fade-in-down">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="3">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                    {{ session('profile-status') }}
                                </span>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Logout Button (Mobile Only Position) -->
                <button wire:click="logout"
                    class="flex items-center justify-center w-full gap-3 py-5 text-xl font-black text-red-600 transition-colors border-2 border-red-100 shadow-sm lg:hidden bg-red-50 rounded-2xl hover:bg-red-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                    تسجيل الخروج
                </button>
            </div>

            <!-- COLUMN 2: Security Settings -->
            <div class="space-y-8">

                <!-- Password Change -->
                <div class="bg-white p-6 md:p-8 rounded-[2rem] shadow-sm border border-gray-200">
                    <h3 class="flex items-center gap-3 mb-6 text-xl font-black text-gray-800">
                        <div class="bg-orange-100 p-2.5 rounded-xl text-orange-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                        الأمان وكلمة المرور
                    </h3>

                    <form wire:submit="updatePassword" class="space-y-5">
                        <div>
                            <label class="block mb-2 text-base font-bold text-gray-700">كلمة المرور الحالية</label>
                            <input type="password" wire:model="current_password" placeholder="••••••"
                                class="w-full px-5 py-4 text-lg font-bold placeholder-gray-300 transition-all border-2 border-gray-200 bg-gray-50 rounded-2xl focus:ring-orange-500 focus:border-orange-500">
                            @error('current_password') <span
                                class="flex items-center block gap-1 mt-1 text-sm font-bold text-red-600"><svg
                                    class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-base font-bold text-gray-700">كلمة المرور الجديدة</label>
                                <input type="password" wire:model="password" placeholder="••••••"
                                    class="w-full px-5 py-4 text-lg font-bold placeholder-gray-300 transition-all border-2 border-gray-200 bg-gray-50 rounded-2xl focus:ring-orange-500 focus:border-orange-500">
                                @error('password') <span
                                    class="flex items-center block gap-1 mt-1 text-sm font-bold text-red-600"><svg
                                        class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block mb-2 text-base font-bold text-gray-700">تأكيد كلمة المرور</label>
                                <input type="password" wire:model="password_confirmation" placeholder="••••••"
                                    class="w-full px-5 py-4 text-lg font-bold placeholder-gray-300 transition-all border-2 border-gray-200 bg-gray-50 rounded-2xl focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>

                        <div class="flex flex-col items-center justify-between gap-4 pt-4 sm:flex-row">
                            <button type="submit"
                                class="flex items-center justify-center w-full gap-2 px-8 py-4 text-lg font-black text-white transition-all bg-orange-500 shadow-lg sm:w-auto rounded-xl hover:bg-orange-600 active:scale-95">
                                <span wire:loading.remove wire:target="updatePassword">تحديث كلمة المرور</span>
                                <span wire:loading wire:target="updatePassword">جاري...</span>
                                <svg wire:loading.remove xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path
                                        d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4" />
                                </svg>
                            </button>

                            @if (session()->has('password-status'))
                                <span
                                    class="flex items-center justify-center w-full gap-2 px-4 py-3 text-base font-bold text-green-700 bg-green-100 border border-green-200 sm:w-auto rounded-xl animate-fade-in-down">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="3">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                    {{ session('password-status') }}
                                </span>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Report PIN -->
                <div class="bg-white p-6 md:p-8 rounded-[2rem] shadow-sm border border-gray-200">
                    <h3 class="flex items-center gap-3 mb-4 text-xl font-black text-gray-800">
                        <div class="bg-indigo-100 p-2.5 rounded-xl text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                        كود قفل التقارير (PIN)
                    </h3>
                    <div class="p-4 mb-6 border border-indigo-100 bg-indigo-50 rounded-xl">
                        <p class="flex items-start gap-2 text-sm font-bold leading-relaxed text-indigo-900">
                            <span>🔒</span>
                            هذا الكود مكون من أرقام فقط، وسيتم طلبه منك عند محاولة الدخول لصفحة التقارير لضمان سرية
                            البيانات.
                        </p>
                    </div>

                    <form wire:submit="updateReportPin" class="space-y-4">
                        <div>
                            <div class="flex flex-col gap-4 sm:flex-row">

                                <input type="password" inputmode="numeric" wire:model="report_pin" placeholder="1234"
                                    class="w-full px-5 py-4 text-lg font-bold placeholder-gray-300 transition-all border-2 border-gray-200 bg-gray-50 rounded-2xl focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div class="flex flex-col items-center justify-between gap-4 pt-4 sm:flex-row">
                                <button type="submit"
                                    class="flex items-center justify-center h-16 gap-2 px-8 text-lg font-black text-white transition-colors bg-indigo-600 shadow-md rounded-2xl hover:bg-indigo-700 whitespace-nowrap">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="3">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                    حفظ الكود
                                </button>

                                @error('report_pin') <span
                                    class="flex items-center block gap-1 mt-2 text-sm font-bold text-red-600"><svg
                                        class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>{{ $message }}</span> @enderror
                                @if (session()->has('pin-status'))
                                    <span
                                        class="flex items-center justify-center gap-2 px-4 py-3 mt-4 text-base font-bold text-green-700 bg-green-100 border border-green-200 rounded-xl animate-fade-in-down">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="3">
                                            <polyline points="20 6 9 17 4 12" />
                                        </svg>
                                        {{ session('pin-status') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <!-- ==================================================== -->
                <!-- 👑 كارت إدارة النظام (يظهر للأدمن فقط) -->
                <!-- ==================================================== -->
                @if(Auth::user()->role === 'admin')
                    <div class="bg-gradient-to-br p-6 md:p-8 rounded-[2rem] shadow-xl border border-purple-700 ">
                        <h3 class="flex items-center gap-3 mb-3 text-xl font-black ">
                            <div class="p-2 rounded-xl ">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="m9 16 2 2 4-4"/>
                                </svg>
                            </div>
                             أسابيع التتميم
                        </h3>

                        <p class="mb-6 text-sm font-bold leading-relaxed ">
                            الضغط على هذا الزر سيقوم بتشغيل الأمر  لفتح أسبوع تتميم جديد لجميع العائلات في قاعدة البيانات.
                        </p>

                        <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                            <!-- السحر كله في سطر: wire:confirm -->
                            <button type="button"
                                    wire:click="openNewWeek"
                                    wire:confirm="تنبيه هام: هل أنت متأكد من فتح أسبوع خدمة جديد الآن؟ (هذا الإجراء سيُطبق فوراً على كل العائلات)"
                                    class="flex items-center justify-center w-full gap-2 px-6 py-4 text-lg font-black text-purple-900 transition-all bg-amber-400 shadow-lg sm:w-auto rounded-2xl hover:bg-amber-300 active:scale-95">

                                <span wire:loading.remove wire:target="openNewWeek"> فتح أسبوع جديد الآن</span>
                                <span wire:loading wire:target="openNewWeek">جاري الإنشاء...</span>
                            </button>

                            @if (session()->has('week-status'))
                                <span class="flex items-center justify-center w-full gap-2 px-4 py-3 text-sm font-bold text-green-200 bg-green-900/60 border border-green-500/30 sm:w-auto rounded-xl animate-fade-in-down">
                                    {{ session('week-status') }}
                                </span>
                            @endif

                            @if (session()->has('week-error'))
                                <span class="flex items-center justify-center w-full gap-2 px-4 py-3 text-sm font-bold text-red-200 bg-red-900/60 border border-red-500/30 sm:w-auto rounded-xl">
                                    {{ session('week-error') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Desktop Logout Button -->
                <button wire:click="logout"
                    class="items-center justify-center hidden w-full gap-3 py-5 text-xl font-black text-red-600 transition-colors border-2 border-red-100 shadow-sm lg:flex bg-red-50 rounded-2xl hover:bg-red-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                    تسجيل الخروج من الحساب
                </button>

                <button wire:click="aboutMe"
                    class="items-center justify-center w-full gap-3 py-5 text-xl font-black transition-colors border-2 shadow-sm border-100 text-600 lg:flex bg-50 rounded-2xl hover:bg-gray-100">
                    about us
                </button>

                <p class="mt-6 text-sm font-bold text-center text-gray-300">Talmaza App v1.0.0</p>
            </div>

        </div>

    </div>
</div>
