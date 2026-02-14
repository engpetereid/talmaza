<div class="min-h-screen pb-24 bg-gray-50">

    <!-- Header & Search -->
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-4xl px-4 py-4 mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 mb-4 md:flex-row md:items-center">
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center justify-center w-12 h-12 text-gray-700 transition-colors bg-gray-100 rounded-2xl hover:bg-gray-200" aria-label="الرئيسية">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-black leading-tight text-gray-900 md:text-3xl">لوحة القرارات 📢</h1>
                        <p class="mt-1 text-sm font-medium text-gray-500">القرارات الرسمية وملخصات التدبير</p>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="ابحث في عنوان أو نص القرار..."
                       class="w-full py-3 pl-4 pr-12 text-base font-bold text-gray-900 placeholder-gray-400 transition-all bg-gray-100 border-2 border-transparent shadow-inner focus:bg-white focus:border-indigo-500 rounded-xl">
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                    <div wire:loading.remove wire:target="search">
                        <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                    <div wire:loading wire:target="search">
                        <svg class="w-6 h-6 text-indigo-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl px-4 py-8 mx-auto space-y-8 sm:px-6 lg:px-8">

        <!-- Create Post Form (Admin Only) -->
        @if(Auth::user()->role == 'admin')
            <div class="p-6 bg-white border border-indigo-100 shadow-lg md:p-8 rounded-3xl animate-fade-in-up">
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex items-center justify-center w-10 h-10 text-xl font-bold text-indigo-600 bg-indigo-100 rounded-full">✏️</div>
                    <h3 class="text-xl font-black text-gray-900">نشر قرار جديد</h3>
                </div>

                <form wire:submit="postAnnouncement" class="space-y-5">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">عنوان القرار</label>
                        <input type="text" wire:model="title" placeholder="مثال: موعد القداس القادم..."
                               class="w-full px-4 py-3 text-lg font-bold text-gray-900 placeholder-gray-400 transition-all border-2 border-gray-200 bg-gray-50 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                        @error('title') <p class="mt-1 text-sm font-bold text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">نص القرار</label>
                        <textarea wire:model="content" rows="4" placeholder="التفاصيل..."
                                  class="w-full px-4 py-3 text-lg font-medium leading-relaxed text-gray-900 placeholder-gray-400 transition-all border-2 border-gray-200 resize-none bg-gray-50 rounded-xl focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        @error('content') <p class="mt-1 text-sm font-bold text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col items-center justify-between gap-4 pt-2 sm:flex-row">
                        <!-- File Upload -->
                        <div x-data="{ uploading: false, progress: 0 }"
                             x-on:livewire-upload-start="uploading = true"
                             x-on:livewire-upload-finish="uploading = false"
                             x-on:livewire-upload-error="uploading = false"
                             x-on:livewire-upload-progress="progress = $event.detail.progress"
                             class="w-full sm:w-auto">

                            <label class="flex items-center justify-center w-full gap-2 px-6 py-3 text-gray-500 transition-all border-2 border-gray-300 border-dashed cursor-pointer sm:justify-start bg-gray-50 rounded-xl hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-600 sm:w-auto group">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-hover:scale-110"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <span class="text-sm font-bold">
                                    {{ $attachment ? 'تم اختيار الملف ✅' : 'إرفاق ملف (PDF/صورة)' }}
                                </span>
                                <input type="file" wire:model="attachment" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            </label>

                            <!-- Progress Bar -->
                            <div x-show="uploading" class="w-full h-2 mt-2 overflow-hidden bg-gray-200 rounded-full">
                                <div class="h-2 transition-all duration-300 bg-indigo-600 rounded-full" :style="'width: ' + progress + '%'"></div>
                            </div>
                            @error('attachment') <p class="mt-1 text-sm font-bold text-center text-red-600 sm:text-right">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" wire:loading.attr="disabled"
                                class="w-full sm:w-auto bg-indigo-600 text-white px-8 py-3 rounded-xl font-black shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove>نشر الآن</span>
                            <span wire:loading>جاري النشر...</span>
                            <svg wire:loading.remove xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        </button>
                    </div>
                </form>

                @if (session()->has('message'))
                    <div class="flex items-center gap-3 p-4 mt-6 font-bold text-green-800 bg-green-100 border-r-4 border-green-500 rounded-lg animate-fade-in-down">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        {{ session('message') }}
                    </div>
                @endif
            </div>
        @endif

        <!-- Posts List -->
        <div class="space-y-6">
            @forelse($posts as $post)
                <div class="overflow-hidden transition-shadow bg-white border border-gray-100 shadow-sm rounded-3xl hover:shadow-md">

                    <!-- Post Header -->
                    <div class="flex items-start justify-between p-6 bg-white border-b md:p-8 border-gray-50">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center justify-center text-2xl font-black text-red-600 border border-red-100 shadow-sm w-14 h-14 rounded-2xl bg-red-50">
                                📢
                            </div>
                            <div>
                                <h4 class="mb-1 text-xl font-black leading-tight text-gray-900 md:text-2xl">{{ $post->title }}</h4>
                                <div class="flex items-center gap-2 text-sm font-medium text-gray-500">
                                    <span class="flex items-center gap-1 bg-gray-100 px-2 py-0.5 rounded text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        {{ $post->user->name ?? 'الإدارة' }}
                                    </span>
                                    <span>•</span>
                                    <span>{{ $post->created_at->locale('ar')->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        @if(Auth::user()->role == 'admin')
                            <button wire:confirm="هل أنت متأكد من حذف هذا القرار؟"
                                    wire:click="deletePost({{ $post->id }})"
                                    class="flex items-center justify-center w-10 h-10 text-gray-300 transition-colors rounded-xl hover:text-red-600 hover:bg-red-50" title="حذف المنشور">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                            </button>
                        @endif
                    </div>

                    <!-- Post Content -->
                    <div class="p-6 md:p-8">
                        <div class="font-medium leading-relaxed prose prose-lg text-gray-700 whitespace-pre-line max-w-none">
                            {{ $post->content }}
                        </div>
                    </div>

                    <!-- Attachments -->
                    @if($post->attachment)
                        @php
                            $extension = pathinfo($post->attachment, PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp

                        <div class="p-4 border-t-2 border-gray-100 bg-gray-50/50 md:p-6">
                            @if($isImage)
                                <div class="overflow-hidden border border-gray-200 shadow-sm rounded-2xl">
                                    <img src="{{ asset('storage/' . $post->attachment) }}" alt="مرفق" class="w-full h-auto object-cover max-h-[500px]">
                                </div>
                            @else
                                <!-- File Download Card -->
                                <a href="{{ asset('storage/' . $post->attachment) }}" target="_blank" class="flex items-center gap-4 p-4 transition-all bg-white border border-gray-200 shadow-sm rounded-2xl hover:border-indigo-300 hover:shadow-md group">
                                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 text-red-600 transition-colors rounded-xl bg-red-50 group-hover:bg-red-600 group-hover:text-white">
                                        @if(in_array(strtolower($extension), ['pdf']))
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="M9 15l2 2 4-4"/></svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <h5 class="text-lg font-bold text-gray-900 transition-colors group-hover:text-indigo-600">تحميل الملف المرفق</h5>
                                        <p class="text-sm font-bold text-gray-500 uppercase">{{ $extension }} File</p>
                                    </div>
                                    <div class="p-2 mr-auto text-gray-500 transition-colors bg-gray-100 rounded-lg group-hover:bg-indigo-600 group-hover:text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    </div>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="py-24 text-center bg-white border-2 border-gray-200 border-dashed rounded-3xl">
                    <div class="flex items-center justify-center w-24 h-24 mx-auto mb-6 text-5xl rounded-full opacity-50 bg-gray-50 grayscale">🔍</div>
                    <h3 class="mb-2 text-xl font-bold text-gray-800">لا توجد قرارات حالياً</h3>
                    <p class="font-medium text-gray-500">لم يتم نشر أي قرارات عامة بعد، أو لا توجد نتائج لبحثك.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-8">
            {{ $posts->links() }}
        </div>

    </div>
</div>
