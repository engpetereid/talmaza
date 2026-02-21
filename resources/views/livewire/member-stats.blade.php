<div class="min-h-screen pb-20 bg-gray-50">

    <script src="{{ asset('build/assets/chart.js')}}"></script>

    <!-- 1. Upper Profile Card -->
    <div
        class="bg-indigo-700 pb-20 pt-8 px-6 rounded-b-[3rem] shadow-xl relative z-0 text-white transition-all duration-300">
        <div class="mx-auto max-w-7xl">

            <!-- Header (Navigation) -->
            <div class="flex items-center justify-between mb-10">
                <a href="{{ url()->previous() }}" wire:navigate
                    class="flex items-center gap-2 px-5 py-3 text-base font-bold transition-colors border rounded-2xl bg-white/10 hover:bg-white/20 backdrop-blur-md border-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                    <span>رجوع</span>
                </a>

                <h1 class="hidden text-xl font-bold opacity-90 md:block">
                    {{ $isEditing ? 'تعديل البيانات' : 'الملف الشخصي' }}
                </h1>

                <button wire:click="toggleEdit"
                    class="flex items-center gap-2 px-5 py-3 text-base font-bold transition-colors border rounded-2xl bg-white/10 hover:bg-white/20 backdrop-blur-md border-white/10">
                    <span>{{ $isEditing ? 'إلغاء' : 'تعديل' }}</span>
                    @if($isEditing)
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5">
                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                            <path d="m15 5 4 4" />
                        </svg>
                    @endif
                </button>
            </div>

            @if (session()->has('message'))
                <div
                    class="flex items-center justify-center max-w-2xl gap-2 p-4 mx-auto mb-8 text-lg font-bold text-center text-white shadow-lg bg-green-500/90 rounded-2xl">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="3">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    {{ session('message') }}
                </div>
            @endif

            <!-- Profile Content -->
            <div class="flex flex-col items-center transition-all md:flex-row md:items-start md:gap-10">

                <!-- Avatar Section -->
                <div class="flex flex-col items-center">
                    <div
                        class="relative flex items-center justify-center flex-shrink-0 w-32 h-32 mb-3 overflow-hidden text-5xl font-black text-indigo-700 bg-white border-4 border-indigo-300 rounded-full shadow-2xl md:w-40 md:h-40 md:text-6xl group">

                        <!-- إظهار الصورة إذا تم اختيارها الآن (مؤقتة) -->
                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="object-cover w-full h-full">
                            <!-- إظهار الصورة المحفوظة في قاعدة البيانات -->
                        @elseif ($member->photo_path)
                            <img src="{{ asset('storage/' . $member->photo_path) }}" class="object-cover w-full h-full">
                            <!-- الحرف الأول إذا لم توجد صورة -->
                        @else
                            {{ mb_substr($member->name, 0, 1) }}
                        @endif

                        <!-- طبقة التعديل (تظهر فقط في وضع التعديل) -->

                        @if($isEditing)
                            <label
                                class="absolute inset-0 flex flex-col items-center justify-center text-white transition-opacity opacity-0 cursor-pointer bg-black/60 group-hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                                    <circle cx="12" cy="13" r="4" />
                                </svg>
                                <span class="mt-2 text-xs font-bold">تغيير الصورة</span>
                                <input type="file" wire:model="photo" accept="image/*" class="hidden">
                            </label>
                        @endif
                    </div>

                    <!-- رسالة تحميل الصورة (Livewire) -->
                    <div wire:loading wire:target="photo" class="mb-3 text-xs font-bold text-indigo-200 animate-pulse">
                        جاري رفع الصورة...</div>

                    <!-- رسالة خطأ الصورة -->
                    @error('photo') <span class="mb-3 text-xs font-bold text-red-300">{{ $message }}</span> @enderror

                    <!-- زر حذف الصورة -->
                    @if($isEditing && $member->photo_path)
                        <button wire:click="deletePhoto" wire:confirm="هل أنت متأكد من حذف الصورة؟"
                            class="px-4 py-1.5 text-xs font-bold text-red-200 transition-colors bg-white/10 rounded-full hover:bg-red-500 hover:text-white mb-6 md:mb-0">
                            حذف الصورة 🗑️
                        </button>
                    @endif
                </div>

                <!-- Info Section -->
                <div class="flex-grow w-full text-center md:text-right">
                    @if($isEditing)
                        <div
                            class="grid grid-cols-1 gap-6 p-8 border shadow-inner md:grid-cols-2 bg-indigo-800/50 rounded-3xl backdrop-blur-md border-indigo-400/30">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-right text-indigo-200">الاسم</label>
                                <input type="text" wire:model="name"
                                    class="w-full p-4 text-lg font-bold text-indigo-900 bg-white border-0 shadow-sm rounded-xl focus:ring-4 focus:ring-indigo-400"
                                    placeholder="الاسم رباعي">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-right text-indigo-200">رقم الموبايل</label>
                                <input type="tel" wire:model="phone"
                                    class="w-full p-4 text-lg font-bold text-right text-indigo-900 bg-white border-0 shadow-sm rounded-xl focus:ring-4 focus:ring-indigo-400"
                                    placeholder="01xxxxxxxxx">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-right text-indigo-200">تاريخ الميلاد</label>
                                <input type="date" wire:model="birth_date"
                                    class="w-full p-4 text-lg font-bold text-indigo-900 bg-white border-0 shadow-sm rounded-xl focus:ring-4 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-right text-indigo-200">الكلية / العمل</label>
                                <input type="text" wire:model="job_or_college"
                                    class="w-full p-4 text-lg font-bold text-indigo-900 bg-white border-0 shadow-sm rounded-xl focus:ring-4 focus:ring-indigo-400"
                                  placeholder="العمل أو الدراسة">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-right text-indigo-200">أب الاعتراف</label>
                                <input type="text" wire:model="confession_father"
                                    class="w-full p-4 text-lg font-bold text-indigo-900 bg-white border-0 shadow-sm rounded-xl focus:ring-4 focus:ring-indigo-400"
                                    placeholder="اسم أب الاعتراف">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-right text-indigo-200">المواهب</label>
                                <input type="text" wire:model="talents"
                                    class="w-full p-4 text-lg font-bold text-indigo-900 bg-white border-0 shadow-sm rounded-xl focus:ring-4 focus:ring-indigo-400"
                                    placeholder="رسم، ترانيم، ...">
                            </div>
                            <button wire:click="saveProfile"
                                class="flex items-center justify-center col-span-1 gap-2 py-4 mt-4 text-xl font-black text-white transition-all transform bg-green-500 shadow-lg md:col-span-2 hover:bg-green-600 rounded-xl active:scale-95">
                                <span wire:loading.remove wire:target="saveProfile">حفظ التعديلات</span>
                                <span wire:loading wire:target="saveProfile">جاري الحفظ...</span>
                                <svg wire:loading.remove wire:target="saveProfile" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="3">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </button>
                        </div>
                    @else
                        <!-- View Mode -->
                        <h2 class="mb-4 text-3xl font-black tracking-tight md:text-5xl">{{ $member->name }}</h2>

                        <div class="flex flex-wrap items-center justify-center gap-4 mb-6 md:justify-start">
                            @if($member->phone)
                                <a href="tel:{{ $member->phone }}"
                                    class="flex items-center gap-2 bg-white/20 hover:bg-white/30 px-5 py-2.5 rounded-full text-base font-bold backdrop-blur-sm transition-colors border border-white/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                    </svg>
                                    {{ $member->phone }}
                                </a>
                                <a href="https://wa.me/+20{{ ltrim($member->phone, '0') }}" target="_blank"
                                    class="flex items-center justify-center transition-colors bg-green-500 rounded-full shadow-lg w-11 h-11 hover:bg-green-600"
                                    title="Whatsapp">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round" class="text-white">

                                        <path
                                            d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                                    </svg>
                                </a>
                            @endif
                            <span
                                class="px-5 py-2.5 rounded-full text-base font-bold border {{ $member->is_active ? 'bg-green-500/20 text-green-100 border-green-400/30' : 'bg-red-500/20 text-red-100 border-red-400/30' }}">
                                {{ $member->is_active ? 'نشط ✅' : 'غير نشط 🚫' }}
                            </span>
                        </div>

                        <div
                            class="grid max-w-3xl grid-cols-1 mx-auto text-base font-medium text-indigo-100 md:grid-cols-2 gap-y-3 gap-x-8 md:mx-0">
                            @if($member->job_or_college)
                                <div
                                    class="flex items-center justify-center gap-3 p-2 transition-colors rounded-lg md:justify-start hover:bg-white/5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M22 10v6M2 10v6" />
                                        <path
                                            d="M20 21a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3zm2-5a2 2 0 0 0-2-2h-2V8a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v4H4a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2z" />
                                    </svg>
                                    {{ $member->job_or_college }}
                                </div>
                            @endif
                            @if($member->confession_father)
                                <div
                                    class="flex items-center justify-center gap-3 p-2 transition-colors rounded-lg md:justify-start hover:bg-white/5">
                                    <span class="font-bold text-indigo-300">أب الاعتراف:</span> {{ $member->confession_father }}
                                </div>
                            @endif
                            @if($member->birth_date)
                                <div
                                    class="flex items-center justify-center gap-3 p-2 transition-colors rounded-lg md:justify-start hover:bg-white/5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2.5">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                    {{ $member->birth_date->format('d F') }} ({{ $member->birth_date->age }} سنة)
                                </div>
                            @endif
                            @if($member->talents)
                                <div
                                    class="flex items-center justify-center col-span-1 gap-3 p-2 transition-colors rounded-lg md:col-span-2 md:justify-start hover:bg-white/5">
                                    <span class="font-bold text-indigo-300">المواهب:</span> {{ $member->talents }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Analytics Content -->
    <div class="relative z-10 px-4 mx-auto -mt-20 space-y-10 max-w-7xl sm:px-6 lg:px-8">

        <!-- Time Filters -->
        <div
            class="flex flex-wrap justify-between max-w-3xl gap-3 p-3 mx-auto overflow-x-auto bg-white border border-gray-100 shadow-xl rounded-2xl sm:flex-nowrap no-scrollbar">
            @php $periods = [4 => 'شهر', 12 => '3 شهور', 24 => '6 شهور', 52 => 'سنة']; @endphp
            @foreach($periods as $val => $label)

                <button wire:click="setPeriod({{ $val }})"
                    class="flex-1 py-4 px-6 rounded-xl text-base font-black transition-all whitespace-nowrap {{ $period == $val ? 'bg-indigo-600 text-white shadow-lg transform scale-105' : 'text-gray-500 hover:bg-gray-100' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <!-- 3. Stats Grid (Responsive) -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-6 md:p-10">
            <h3
                class="flex items-center justify-center gap-3 mb-8 text-sm font-bold tracking-wider text-center text-gray-400 uppercase">
                <span class="w-8 h-px bg-gray-200"></span>
                متوسط آخر {{ $meetingsCount }} اجتماعات
                <span class="w-8 h-px bg-gray-200"></span>
            </h3>

            <div class="grid grid-cols-2 gap-4 text-center sm:grid-cols-3 md:grid-cols-5 md:gap-8">
                <!-- Primary Stats -->
                <div class="flex flex-col justify-center p-4 border border-blue-100 bg-blue-50 rounded-3xl">
                    <div class="mb-1 text-xs font-black text-blue-600 uppercase">الحضور</div>
                    <div class="text-3xl font-black text-blue-800">{{ $metrics['attendance']['average'] }}%</div>
                </div>
                <div class="flex flex-col justify-center p-4 border border-purple-100 bg-purple-50 rounded-3xl">
                    <div class="mb-1 text-xs font-black text-purple-600 uppercase">النوتة</div>
                    <div class="text-3xl font-black text-purple-800">{{ $metrics['note']['average'] }}%</div>
                </div>
                <div class="flex flex-col justify-center p-4 border border-orange-100 bg-orange-50 rounded-3xl">
                    <div class="mb-1 text-xs font-black text-orange-600 uppercase">القداس</div>
                    <div class="text-3xl font-black text-orange-800">{{ $metrics['mass']['average'] }}%</div>
                </div>
                <div class="flex flex-col justify-center p-4 border border-yellow-100 bg-yellow-50 rounded-3xl">
                    <div class="mb-1 text-xs font-black text-yellow-600 uppercase">تدريب التلمذة</div>
                    <div class="text-3xl font-black text-yellow-800">{{ $metrics['training']['average'] }}%</div>
                </div>
                <div class="flex flex-col justify-center p-4 border border-teal-100 bg-teal-50 rounded-3xl">
                    <div class="mb-1 text-xs font-black text-teal-600 uppercase">القراءة</div>
                    <div class="text-3xl font-black text-teal-800">{{ $metrics['reading']['average'] }}%</div>
                </div>

                <!-- Secondary Stats Line -->
                <div class="col-span-2 my-2 border-t border-gray-100 sm:col-span-3 md:col-span-5"></div>

                <div class="pt-2">
                    <div class="mb-1 text-sm font-bold text-gray-500">مشاركة الخلوة</div>
                    <div class="text-2xl font-black text-gray-800">{{ $metrics['kholwa']['average'] }}%</div>
                </div>
                <div class="pt-2">
                    <div class="mb-1 text-sm font-bold text-gray-500">عشية/تسبحة</div>
                    <div class="text-2xl font-black text-gray-800">{{ $metrics['vespers']['average'] }}%</div>
                </div>
                <div class="pt-2">
                    <div class="mb-1 text-sm font-bold text-gray-500">اجتماع خدام</div>
                    <div class="text-2xl font-black text-gray-800">{{ $metrics['servants']['average'] }}%</div>
                </div>
                <div class="pt-2">
                    <div class="mb-1 text-sm font-bold text-gray-500">مذبح عائلي</div>
                    <div class="text-2xl font-black text-gray-800">{{ $metrics['altar']['average'] }}%</div>

                </div>
                <div class="pt-2">
                    <div class="mb-1 text-sm font-bold text-gray-500">خلوة اسبوعية</div>
                    <div class="text-2xl font-black text-gray-800">{{ $metrics['weekly_kholwa']['average'] }}%</div>
                </div>
            </div>
        </div>

        <!-- 4. Detailed Charts (Responsive Grid) -->
        <div>
            <h3 class="flex items-center gap-3 px-2 mb-6 text-2xl font-black text-gray-900">
                <span class="w-3 h-8 bg-indigo-600 rounded-full"></span>
                التحليل البياني المفصل
            </h3>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3"
                wire:key="charts-{{ $period }}">
                @foreach($metrics as $key => $metric)
                    <div
                        class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center justify-between mb-6">
                            <h4 class="text-lg font-black text-gray-800">{{ $metric['label'] }}</h4>
                            <span class="text-sm font-black px-4 py-1.5 rounded-full bg-gray-50 border border-gray-100"
                                style="color: {{ $metric['color'] }}">
                                {{ $metric['average'] }}%
                            </span>
                        </div>

                        <div class="relative w-full h-56" x-data x-init='
                                                                    if ($el.chart) { $el.chart.destroy(); }
                                                                    $el.chart = new Chart($el.querySelector("canvas"), {
                                                                        type: "line",
                                                                        data: {
                                                                            labels: @json($chartLabels),
                                                                            datasets: [{
                                                                                label: "{{ $metric["label"] }}",
                                                                                data: @json($metric["trend"]),
                                                                                borderColor: "{{ $metric["color"] }}",
                                                                                backgroundColor: "{{ $metric["color"] }}15",
                                                                                borderWidth: 3,
                                                                                fill: true,
                                                                                tension: 0.35,
                                                                                pointRadius: 4,
                                                                                pointHoverRadius: 6,
                                                                                pointBackgroundColor: "#fff",
                                                                                pointBorderColor: "{{ $metric["color"] }}",
                                                                                pointBorderWidth: 2
                                                                            }]
                                                                        },
                                                                        options: {
                                                                            responsive: true,
                                                                            maintainAspectRatio: false,
                                                                            plugins: {
                                                                                legend: { display: false },
                                                                                tooltip: {
                                                                                    backgroundColor: "rgba(255, 255, 255, 0.98)",
                                                                                    titleColor: "#111827",
                                                                                    bodyColor: "{{ $metric["color"] }}",
                                                                                    borderColor: "#e5e7eb",
                                                                                    borderWidth: 1,
                                                                                    padding: 14,
                                                                                    titleFont: { size: 14, weight: "bold", family: "sans-serif" },

                                bodyFont: { size: 14, weight: "bold", family: "sans-serif" },
                                                                                    displayColors: false,
                                                                                    callbacks: {
                                                                                        label: function(context) {
                                                                                            return context.parsed.y + "%";
                                                                                        }
                                                                                    }
                                                                                }
                                                                            },
                                                                            scales: {
                                                                                y: {
                                                                                    beginAtZero: true,
                                                                                    max: 100,
                                                                                    ticks: { stepSize: 25, font: { size: 12, weight: "bold" }, color: "#9ca3af" },
                                                                                    grid: { color: "#f3f4f6" },
                                                                                    border: { display: false }
                                                                                },
                                                                                x: {
                                                                                    grid: { display: false },
                                                                                    ticks: { font: { size: 11, weight: "bold" }, maxRotation: 0, autoSkip: true, maxTicksLimit: 6, color: "#9ca3af" },
                                                                                    border: { display: false }
                                                                                }
                                                                            },
                                                                            interaction: {
                                                                                mode: "index",
                                                                                intersect: false
                                                                            }
                                                                        }
                                                                    });
                                                                 '>
                            <canvas></canvas>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 5. Timeline (Responsive) -->
        <div class="pb-12">
            <h3 class="flex items-center gap-3 px-2 mb-6 text-2xl font-black text-gray-900">
                <span class="w-3 h-8 bg-indigo-600 rounded-full"></span>
                سجل الحضور الأخير
                <span
                    class="px-3 py-1 text-sm font-medium text-gray-400 bg-gray-100 rounded-full">{{ count($history) }}</span>
            </h3>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                @forelse($history as $record)
                    <div
                        class="flex items-center gap-5 p-5 transition-all bg-white border border-gray-100 shadow-sm rounded-3xl hover:border-indigo-200 group">
                        <div
                            class="w-14 h-14 rounded-2xl flex items-center justify-center font-black text-2xl flex-shrink-0 {{ $record['present'] ? 'bg-green-100 text-green-700' : 'bg-red-50 text-red-500' }}">
                            {{ $record['present'] ? '✔' : '✕' }}
                        </div>
                        <div class="flex-grow">
                            <h4
                                class="mb-1 text-base font-bold text-gray-900 transition-colors group-hover:text-indigo-700">
                                {{ \Carbon\Carbon::parse($record['date'])->locale('ar')->isoFormat('D MMMM YYYY') }}
                            </h4>
                            <div class="flex items-center gap-2">
                                @if($record['present'])
                                    <span
                                        class="px-3 py-1 text-xs font-bold text-purple-700 border border-purple-100 rounded-lg bg-purple-50">

                                        نوتة: {{ $record['note'] }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs font-bold text-gray-400 rounded-lg bg-gray-50">غائب</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full text-center py-16 bg-white rounded-[2.5rem] border-2 border-dashed border-gray-200">
                        <div class="mb-4 text-5xl grayscale opacity-30">📅</div>
                        <p class="text-lg font-bold text-gray-500">لا يوجد سجلات في هذه الفترة</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
