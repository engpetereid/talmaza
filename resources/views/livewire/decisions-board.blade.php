<div class="min-h-screen bg-gray-50 pb-24">

    <!-- Header & Search -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" wire:navigate class="w-12 h-12 flex items-center justify-center rounded-2xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors" aria-label="الرئيسية">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </a>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-black text-gray-900 leading-tight">متابعة القرارات 📋</h1>
                        <p class="text-sm font-medium text-gray-500 mt-1">القرارات الإدارية وحالة تنفيذها</p>
                    </div>
                </div>
            </div>

            <!-- Filters Row -->
            <div class="flex flex-col md:flex-row gap-3">
                <!-- Search -->
                <div class="relative flex-grow">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="ابحث في القرارات..."
                           class="w-full bg-gray-100 border-2 border-transparent focus:bg-white focus:border-indigo-500 text-gray-900 rounded-xl py-3 pr-10 pl-4 text-sm font-bold transition-all placeholder-gray-400">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                </div>

                <!-- Status Filter -->
                <select wire:model.live="filterStatus" class="bg-white border-2 border-gray-200 text-gray-700 text-sm font-bold rounded-xl focus:ring-indigo-500 focus:border-indigo-500 py-3 px-4">
                    <option value="all">كل الحالات</option>
                    <option value="pending">⏳ قيد المراجعة</option>
                    <option value="implemented">✅ تم التنفيذ</option>
                    <option value="postponed">🟠 مؤجل</option>
                    <option value="not_implemented">❌ لم يتم التنفيذ</option>
                </select>

                <!-- Month Filter -->
                <select wire:model.live="filterMonth" class="bg-white border-2 border-gray-200 text-gray-700 text-sm font-bold rounded-xl focus:ring-indigo-500 focus:border-indigo-500 py-3 px-4">
                    <option value="all">كل الشهور</option>
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}">{{ Carbon\Carbon::create()->month($i)->locale('ar')->monthName }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- Create Post Form (Admin Only) -->
        @if(Auth::user()->role == 'admin')
            <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-indigo-100 animate-fade-in-up">
                <h3 class="text-xl font-black text-gray-900 mb-5 flex items-center gap-2">
                    <span class="bg-indigo-100 text-indigo-600 p-2 rounded-lg">✍️</span>
                    تسجيل قرار جديد
                </h3>

                <form wire:submit="postDecision" class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">عنوان القرار</label>
                        <input type="text" wire:model="title" placeholder="مثال: تطبيق منهج جديد..."
                               class="w-full bg-gray-50 border-2 border-gray-200 text-gray-900 text-lg rounded-xl py-3 px-4 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-bold placeholder-gray-400">
                        @error('title') <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">تفاصيل وخطوات التنفيذ</label>
                        <textarea wire:model="content" rows="3" placeholder="التفاصيل..."
                                  class="w-full bg-gray-50 border-2 border-gray-200 text-gray-900 text-base rounded-xl py-3 px-4 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-medium placeholder-gray-400 resize-none"></textarea>
                        @error('content') <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                        <!-- File Upload -->
                        <div class="w-full sm:w-auto">
                            <label class="flex items-center justify-center sm:justify-start gap-2 px-6 py-3 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-600 transition-all text-gray-500 w-full group">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <span class="font-bold text-sm">{{ $attachment ? 'تم اختيار الملف ✅' : 'إرفاق ملف' }}</span>
                                <input type="file" wire:model="attachment" class="hidden">
                            </label>
                            @error('attachment') <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" wire:loading.attr="disabled"
                                class="w-full sm:w-auto bg-indigo-600 text-white px-8 py-3 rounded-xl font-black shadow-md hover:bg-indigo-700 transition-all flex items-center justify-center gap-2 active:scale-95">
                            <span wire:loading.remove>نشر القرار</span>
                            <span wire:loading>جاري الحفظ...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        @if (session()->has('message'))
            <div class="bg-green-100 border-r-4 border-green-500 text-green-800 p-4 rounded-lg flex items-center gap-3 font-bold animate-fade-in-down">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('message') }}
            </div>
        @endif

        <!-- Decisions List -->
        <div class="space-y-6">
            @forelse($decisions as $decision)
                @php
                    // تحديد ألوان كل حالة
                    $statusConfig = match($decision->status) {
                        'implemented' => ['color' => 'green', 'label' => 'تم التنفيذ ✅'],
                        'not_implemented' => ['color' => 'red', 'label' => 'لم يتم التنفيذ ❌'],
                        'postponed' => ['color' => 'orange', 'label' => 'مؤجل ⏳'],
                        default => ['color' => 'blue', 'label' => 'قيد التنفيذ 🔄'],
                    };
                    $color = $statusConfig['color'];
                @endphp

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative group">
                    <div class="absolute right-0 top-0 bottom-0 w-2 bg-{{ $color }}-500"></div>

                    <!-- Header -->
                    <div class="p-6 md:p-8 pr-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-gray-50">
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="bg-{{ $color }}-100 text-{{ $color }}-700 border border-{{ $color }}-200 px-3 py-1 rounded-lg text-xs font-black uppercase tracking-wider">
                                    {{ $statusConfig['label'] }}
                                </span>
                                <span class="text-xs font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded-md flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    {{ $decision->created_at->locale('ar')->isoFormat('D MMMM YYYY') }}
                                </span>
                            </div>
                            <h4 class="font-black text-xl text-gray-900">{{ $decision->title }}</h4>
                        </div>

                        @if(Auth::user()->role == 'admin')
                            <div class="flex items-center gap-2">
                                <button wire:click="startEdit({{ $decision->id }}, '{{ $decision->status }}', '{{ addslashes($decision->admin_comment) }}')" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white px-4 py-2 rounded-xl text-sm font-bold transition-colors">
                                    تحديث الحالة
                                </button>
                                <button wire:confirm="هل أنت متأكد من حذف هذا القرار؟" wire:click="deletePost({{ $decision->id }})" class="w-10 h-10 flex items-center justify-center rounded-xl text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Inline Edit Form (Admin Only) -->
                    @if($editingDecisionId === $decision->id)
                        <div class="p-6 bg-indigo-50/50 border-b border-indigo-100">
                            <h5 class="text-sm font-black text-indigo-900 mb-3 flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg> تحديث ومتابعة القرار</h5>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <label class="flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-colors {{ $editStatus == 'pending' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-white' }}">
                                        <input type="radio" wire:model="editStatus" value="pending" class="text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm font-bold text-gray-800">قيد التنفيذ</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-colors {{ $editStatus == 'implemented' ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white' }}">
                                        <input type="radio" wire:model="editStatus" value="implemented" class="text-green-600 focus:ring-green-500">
                                        <span class="text-sm font-bold text-gray-800">تم التنفيذ</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-colors {{ $editStatus == 'postponed' ? 'border-orange-500 bg-orange-50' : 'border-gray-200 bg-white' }}">
                                        <input type="radio" wire:model="editStatus" value="postponed" class="text-orange-600 focus:ring-orange-500">
                                        <span class="text-sm font-bold text-gray-800">مؤجل</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-colors {{ $editStatus == 'not_implemented' ? 'border-red-500 bg-red-50' : 'border-gray-200 bg-white' }}">
                                        <input type="radio" wire:model="editStatus" value="not_implemented" class="text-red-600 focus:ring-red-500">
                                        <span class="text-sm font-bold text-gray-800">لم يتم</span>
                                    </label>
                                </div>
                                <textarea wire:model="editComment" rows="2" placeholder="تعقيب أو توضيح لسبب الحالة..." class="w-full bg-white border-2 border-indigo-200 rounded-xl py-3 px-4 focus:ring-indigo-500 focus:border-indigo-500 font-medium"></textarea>
                                <div class="flex gap-2">
                                    <button wire:click="updateDecision" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold shadow-sm hover:bg-indigo-700">حفظ التحديث</button>
                                    <button wire:click="cancelEdit" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-xl font-bold hover:bg-gray-300">إلغاء</button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="p-6 md:p-8 pr-10">
                        <p class="text-gray-700 text-lg font-medium leading-relaxed whitespace-pre-line">{{ $decision->content }}</p>

                        <!-- Admin Comment Display -->
                        @if($decision->admin_comment)
                            <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-2xl flex items-start gap-3">
                                <div class="flex-shrink-0 text-xl">💬</div>
                                <div>
                                    <span class="block text-xs font-black text-gray-500 mb-1">تعقيب الإدارة:</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $decision->admin_comment }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Attachment -->
                        @if($decision->attachment)
                            <div class="mt-6">
                                <a href="{{ asset('storage/' . $decision->attachment) }}" target="_blank" class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-700 border border-indigo-100 px-4 py-2 rounded-xl text-sm font-bold hover:bg-indigo-600 hover:text-white transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    فتح المرفق
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-24 bg-white rounded-3xl border-2 border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl grayscale opacity-50">🔍</div>
                    <h3 class="text-xl font-bold text-gray-800">لا توجد قرارات مطابقة</h3>
                    <p class="text-gray-500 font-medium">حاول تغيير الفلاتر المحددة</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8 flex justify-center">
            {{ $decisions->links() }}
        </div>
    </div>
</div>
