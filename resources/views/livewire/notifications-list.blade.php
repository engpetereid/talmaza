<div class="min-h-screen pb-24 bg-gray-50">

    <!-- Header -->
    <div class="sticky top-0 z-20 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-5xl px-4 py-4 mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" wire:navigate
                        class="flex items-center justify-center w-12 h-12 text-gray-700 transition-colors bg-gray-100 rounded-2xl hover:bg-gray-200"
                        aria-label="الرئيسية">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                    </a>

                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center hidden w-12 h-12 text-indigo-600 rounded-2xl bg-indigo-50 md:flex">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black leading-tight text-gray-900 md:text-3xl">الإشعارات 🔔</h1>
                        <p class="hidden mt-1 text-sm font-medium text-gray-500 md:block">التنبيهات والمناسبات الهامة</p>
                    </div>
                </div>

                @if(Auth::user()->unreadNotifications->count() > 0)
                    <button wire:click="markAllRead" class="flex items-center gap-2 bg-white border-2 border-indigo-100 hover:border-indigo-500 text-indigo-700 px-4 py-2.5 rounded-xl font-bold text-sm transition-all shadow-sm active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span class="hidden sm:inline">تحديد الكل كمقروء</span>
                        <span class="sm:hidden">قراءة الكل</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-5xl px-4 py-8 mx-auto space-y-8 sm:px-6 lg:px-8">

        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="flex items-center gap-3 p-4 font-bold text-green-800 bg-green-100 border-r-4 border-green-500 rounded-xl animate-fade-in-down">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('message') }}
            </div>
        @endif

        <!-- Birthdays Section -->
        @if($birthdays->count() > 0)
            <div class="animate-fade-in-up">
                <h3 class="flex items-center gap-2 px-2 mb-4 text-xl font-black text-gray-800">
                    <span class="text-2xl">🎂</span> أعياد ميلاد اليوم
                </h3>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    @foreach($birthdays as $birthday)
                        <div class="flex items-center justify-between p-5 transition-shadow border border-pink-100 shadow-sm bg-gradient-to-r from-pink-50 via-purple-50 to-white rounded-3xl hover:shadow-md group">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center justify-center text-3xl transition-transform bg-white border-2 border-pink-100 rounded-full shadow-md w-14 h-14 group-hover:scale-110">
                                    🎉
                                </div>
                                <div>
                                    <h4 class="text-lg font-black text-gray-900">{{ $birthday->name }}</h4>
                                    <p class="mt-1 text-sm font-bold text-pink-600">
                                        يتم اليوم {{ \Carbon\Carbon::parse($birthday->birth_date)->age }} سنة! 🎈
                                    </p>
                                    @if(Auth::user()->role === 'admin' && $birthday->family)
                                        <p class="mt-1 text-xs font-medium text-gray-400">{{ $birthday->family->name }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- WhatsApp Action -->
                            @if($birthday->phone)
                                <a href="https://wa.me/+20{{ ltrim($birthday->phone, '0') }}?text=كل سنة وأنت طيب يا {{ explode(' ', $birthday->name)[0] }}! 🎉🎂 عقبال 100 سنة حلوة مع بابا يسوع ❤️"
                                   target="_blank"
                                   class="flex items-center justify-center w-12 h-12 text-white transition-all bg-green-500 shadow-lg rounded-2xl hover:bg-green-600 hover:scale-105 hover:rotate-3" title="أرسل تهنئة">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Notifications List -->
        <div>
            <h3 class="flex items-center gap-2 px-2 mb-6 text-xl font-black text-gray-800">
                <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
                قائمة التنبيهات
            </h3>

            <div class="space-y-4">
                @forelse($notifications as $notification)
                    <div wire:click="markAsRead('{{ $notification->id }}')"
                         class="group relative overflow-hidden p-5 md:p-6 rounded-3xl cursor-pointer transition-all duration-200 border-2
                         {{ $notification->read_at
        ? 'bg-white border-transparent hover:border-gray-200'
        : 'bg-white border-indigo-100 shadow-md ring-2 ring-indigo-50 hover:border-indigo-300' }}">

                        <!-- Unread Indicator Strip -->
                        @if(!$notification->read_at)
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-indigo-500"></div>
                        @endif

                        <div class="flex items-start gap-5">
                            <!-- Icon -->
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 text-2xl shadow-sm transition-colors
                                {{ ($notification->data['type'] ?? '') == 'warning'
        ? 'bg-orange-100 text-orange-600 group-hover:bg-orange-500 group-hover:text-white'
        : 'bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white' }}">
                                @if(($notification->data['type'] ?? '') == 'warning')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                                @endif
                            </div>

                            <div class="flex-grow pt-1">
                                <div class="flex flex-col gap-1 mb-2 sm:flex-row sm:justify-between sm:items-start">
                                    <h4 class="text-base font-bold text-gray-900 transition-colors md:text-lg group-hover:text-indigo-700">
                                        {{ $notification->data['title'] ?? 'إشعار جديد' }}
                                    </h4>
                                    <span class="flex items-center gap-1 px-2 py-1 text-xs font-bold text-gray-400 rounded-lg bg-gray-50 w-fit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <p class="text-sm font-medium leading-relaxed text-gray-600 md:text-base">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-20 bg-white rounded-[2.5rem] border-2 border-dashed border-gray-200 text-center">
                        @if($birthdays->count() == 0)
                            <div class="flex items-center justify-center w-24 h-24 mb-6 text-5xl rounded-full shadow-inner bg-gray-50 grayscale opacity-30">
                                💤
                            </div>
                            <h3 class="mb-2 text-xl font-bold text-gray-800">لا توجد إشعارات حالياً</h3>
                            <p class="max-w-xs mx-auto font-medium text-gray-500"> ستظهر هنا التنبيهات وأعياد الميلاد القادمة.</p>
                        @else
                            <div class="flex items-center justify-center w-20 h-20 mb-4 text-3xl rounded-full bg-gray-50">📭</div>
                            <p class="font-bold text-gray-500">لا توجد إشعارات جديدة</p>
                        @endif
                    </div>
                @endforelse
            </div>

            <div class="flex justify-center mt-8">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
