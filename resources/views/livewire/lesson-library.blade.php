<div class="min-h-screen pb-20 bg-gray-50">

    <!-- Header -->
    <div class="sticky top-0 z-20 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center justify-center w-12 h-12 text-gray-700 transition-colors bg-gray-100 rounded-2xl hover:bg-gray-200" aria-label="الرئيسية">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-black leading-tight text-gray-900 md:text-3xl">مكتبة الدروس 📚</h1>
                        <p class="mt-1 text-sm font-medium text-gray-500">شارك واستفد من خبرات الخدمة</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start h-[calc(100vh-140px)]">

            <!-- LEFT COLUMN: Navigation (Stages & Lessons) -->
            <!-- Scrollable Sidebar on PC -->
            <div class="lg:col-span-4 space-y-4 lg:overflow-y-auto lg:h-full lg:pr-2 {{ $activeLesson ? 'hidden lg:block' : 'block' }}">
                @foreach($stages as $stage)
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 {{ $activeStage === $stage->id ? 'ring-2 ring-indigo-500 ring-offset-2' : 'hover:border-indigo-300' }}">

                        <!-- Stage Header -->
                        <button wire:click="toggleStage({{ $stage->id }})" class="flex items-center justify-between w-full p-5 transition-colors bg-white hover:bg-gray-50">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl {{ $activeStage === $stage->id ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-indigo-600' }} flex items-center justify-center font-black text-xl transition-colors shrink-0">
                                    {{ $stage->order_number }}
                                </div>
                                <span class="text-lg font-bold leading-tight text-right text-gray-900">{{ $stage->name }}</span>
                            </div>
                            <svg class="w-6 h-6 text-gray-400 transition-transform duration-300 {{ $activeStage === $stage->id ? 'rotate-180 text-indigo-600' : '' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                        </button>

                        <!-- Lessons List -->
                        @if($activeStage === $stage->id)
                            <div class="border-t border-gray-100 divide-y divide-gray-100 bg-gray-50/50 animate-slide-down">
                                @foreach($stage->lessons as $lesson)
                                    <button wire:click="selectLesson({{ $lesson->id }})"
                                            class="w-full p-4 pr-6 flex items-center justify-between transition-colors group text-right
                                            {{ $activeLesson && $activeLesson->id === $lesson->id ? 'bg-indigo-50 border-r-4 border-indigo-600' : 'hover:bg-white border-r-4 border-transparent' }}">


                                        <div class="flex items-center gap-3">
                                            <span class="w-2.5 h-2.5 rounded-full shrink-0 {{ $activeLesson && $activeLesson->id === $lesson->id ? 'bg-indigo-600' : 'bg-gray-300 group-hover:bg-indigo-400' }}"></span>
                                            <span class="text-base font-bold leading-snug {{ $activeLesson && $activeLesson->id === $lesson->id ? 'text-indigo-700' : 'text-gray-700 group-hover:text-gray-900' }}">
                                                {{ $lesson->title }}
                                            </span>
                                        </div>

                                        @if($activeLesson && $activeLesson->id === $lesson->id)
                                            <svg class="w-5 h-5 text-indigo-600 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- RIGHT COLUMN: Lesson Details (Responsive) -->
            <!-- On Mobile: Full screen overlay. On Desktop: Main content area -->
            <div class="lg:col-span-8 fixed inset-0 z-50 bg-gray-50 lg:static lg:bg-transparent lg:z-auto flex flex-col transition-transform duration-300 transform {{ $activeLesson ? 'translate-y-0' : 'translate-y-full lg:translate-y-0' }}">

                @if($activeLesson)
                    <!-- Lesson Content Wrapper -->
                    <div class="flex flex-col h-full overflow-hidden lg:h-auto lg:block">

                        <!-- Mobile Close Header -->
                        <div class="sticky top-0 z-20 flex items-center justify-between p-4 bg-white border-b border-gray-200 shadow-sm lg:hidden shrink-0">
                            <h2 class="pl-4 text-lg font-black text-gray-800 truncate">{{ $activeLesson->title }}</h2>
                            <button wire:click="closeLesson" class="p-2 text-gray-600 transition-colors bg-gray-100 rounded-full hover:bg-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                        </div>

                        <!-- Content Body (Scrollable) -->
                        <div class="flex-grow p-4 space-y-6 overflow-y-auto lg:p-0 lg:h-full lg:custom-scrollbar">

                            <!-- Desktop Title -->
                            <div class="hidden p-8 bg-white border border-gray-200 shadow-sm lg:block rounded-3xl">
                                <h2 class="mb-2 text-3xl font-black text-gray-900">{{ $activeLesson->title }}</h2>
                                <p class="text-lg font-medium text-gray-500">شارك المصادر، المذكرات، والملفات المساعدة لهذا الدرس.</p>
                            </div>

                            <!-- Upload Section -->
                            <div class="p-6 border border-indigo-100 shadow-inner bg-indigo-50 rounded-3xl">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="flex items-center justify-center w-12 h-12 text-2xl font-bold text-indigo-600 bg-indigo-100 rounded-2xl">✍️</div>
                                    <h3 class="text-xl font-bold text-indigo-900">شارك تحضيرك</h3>
                                </div>
                                <div class="space-y-4">
                                    <textarea wire:model="newDescription" rows="3" placeholder="اكتب ملخص سريع أو وصف للملف..." class="w-full p-4 text-lg font-medium bg-white border-indigo-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 placeholder-indigo-300/70"></textarea>
                                    @error('newDescription') <p class="text-sm font-bold text-red-600">{{ $message }}</p> @enderror

                                    <div class="flex flex-col gap-4 sm:flex-row">
                                        <!-- Custom File Upload -->
                                        <div class="flex-1"
                                             x-data="{ uploading: false, progress: 0 }"
                                             x-on:livewire-upload-start="uploading = true"
                                             x-on:livewire-upload-finish="uploading = false"
                                             x-on:livewire-upload-error="uploading = false"
                                             x-on:livewire-upload-progress="progress = $event.detail.progress">

                                            <label class="flex items-center justify-center w-full h-full gap-3 px-6 py-4 transition-colors bg-white border-2 border-indigo-300 border-dashed cursor-pointer rounded-xl hover:bg-indigo-100 group">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-400 group-hover:text-indigo-600"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                                <span class="text-base font-bold text-indigo-600 truncate">
                                                    {{ $newFile ? 'تم اختيار الملف ✅' : 'اختر ملف (PDF/Word/صورة)' }}
                                                </span>
                                                <input type="file" wire:model="newFile" class="hidden">
                                            </label>

                                            <!-- Progress Bar -->
                                            <div x-show="uploading" class="w-full bg-indigo-200 rounded-full h-1.5 mt-2 overflow-hidden">
                                                <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                                            </div>
                                            @error('newFile') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                                        </div>

                                        <button wire:click="saveResource" wire:loading.attr="disabled" class="flex items-center justify-center w-full gap-2 px-8 py-4 text-lg font-black text-white transition-transform bg-indigo-600 shadow-lg hover:bg-indigo-700 rounded-xl shadow-indigo-200 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed sm:w-auto">
                                            <span wire:loading.remove>نشر</span>
                                            <span wire:loading>جاري...</span>
                                            <svg wire:loading.remove xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                        </button>
                                    </div>
                                    @if (session()->has('message'))
                                        <div class="flex items-center gap-3 p-4 text-base font-bold text-green-800 bg-green-100 border-r-4 border-green-500 rounded-xl animate-fade-in-down">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                            {{ session('message') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Resources List -->
                            <div class="pb-8 space-y-6">
                                <h3 class="flex items-center gap-3 text-xl font-bold text-gray-800">
                                    <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
                                    مشاركات القادة ({{ $activeLesson->resources->count() }})
                                </h3>

                                @forelse($activeLesson->resources as $resource)
                                    <div class="p-6 transition-all bg-white border border-gray-100 shadow-sm rounded-3xl hover:shadow-md group">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex items-center gap-3">
                                                <div class="flex items-center justify-center w-12 h-12 text-lg font-black text-gray-600 bg-gray-100 border border-gray-200 rounded-2xl">
                                                    {{ mb_substr($resource->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-base font-bold text-gray-900">{{ $resource->user->name }}</p>
                                                    <p class="text-xs font-bold text-gray-400">{{ $resource->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                @if($resource->file_path)
                                                    @php $ext = pathinfo($resource->file_path, PATHINFO_EXTENSION); @endphp
                                                    <a href="{{ asset('storage/' . $resource->file_path) }}" target="_blank" class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-indigo-600 transition-colors border border-gray-200 bg-gray-50 rounded-xl hover:bg-indigo-50 group-hover:border-indigo-200" title="تحميل الملف">
                                                        <span class="uppercase">{{ $ext }}</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                                    </a>
                                                @endif

                                                <!-- Delete Button -->
                                                @if(Auth::id() === $resource->user_id || Auth::user()->role === 'admin')
                                                    <button wire:click="deleteResource({{ $resource->id }})"
                                                            wire:confirm="هل أنت متأكد من حذف هذه المشاركة؟"
                                                            class="flex items-center justify-center w-10 h-10 text-gray-400 transition-colors border border-gray-200 rounded-xl bg-gray-50 hover:text-red-600 hover:bg-red-50 hover:border-red-200"
                                                            title="حذف">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="p-5 text-lg font-medium leading-relaxed text-gray-800 whitespace-pre-line border bg-gray-50/50 rounded-2xl border-gray-50">
                                            {{ $resource->description }}
                                        </div>
                                    <br><br>
                                    </div>

                                @empty
                                    <div class="py-16 text-center bg-white border-2 border-gray-200 border-dashed rounded-3xl">
                                        <div class="flex items-center justify-center w-20 h-20 mx-auto mb-4 text-4xl rounded-full bg-gray-50 grayscale opacity-30">📂</div>
                                        <p class="text-lg font-bold text-gray-500">لم يشارك أحد في هذا الدرس بعد.</p>
                                        <p class="mt-1 font-medium text-gray-400">كن أول المبادرين وشارك تحضيرك! 💪</p>
                                    </div>
                                @endforelse
                            </div>

                        </div>
                    </div>
                @else
                    <!-- PC Placeholder State -->
                    <div class="hidden lg:flex flex-col items-center justify-center h-full text-center p-12 bg-white rounded-3xl border border-gray-200 shadow-sm min-h-[600px]">
                        <div class="flex items-center justify-center w-32 h-32 mb-8 text-6xl rounded-full opacity-50 bg-indigo-50 grayscale animate-pulse">👈</div>
                        <h2 class="mb-3 text-3xl font-black text-gray-800">اختر درساً للبدء</h2>
                        <p class="max-w-md text-xl font-medium leading-relaxed text-gray-500">اضغط على أي درس من القائمة الجانبية لعرض المصادر والمشاركات الخاصة به.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
