<div class="min-h-screen pb-24 bg-gray-50">

    <!-- Header -->
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 mb-4 md:flex-row md:items-center">
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" wire:navigate
                        class="flex items-center justify-center w-12 h-12 text-gray-700 transition-colors bg-gray-100 rounded-2xl hover:bg-gray-200"
                        aria-label="عودة للرئيسية">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-black text-gray-900">تقارير الشهر 📈</h1>
                        <p class="hidden text-sm font-medium text-gray-500 md:block">إحصائيات ونسب المخدومين</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-col w-full gap-3 sm:flex-row md:w-auto">
                    <!-- Year Selector -->
                    <select wire:model.live="year"
                        class="bg-gray-50 border-2 border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 font-bold w-full sm:w-auto transition-colors">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>

                    <!-- Month Selector -->
                    <div class="flex flex-grow gap-2 pb-2 overflow-x-auto sm:pb-0 no-scrollbar">
                        @for ($i = 1; $i <= 12; $i++)
                            <button wire:click="$set('month', {{ $i }})"
                                class="px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all border-2
                                            {{ $month == $i ? 'bg-indigo-600 text-white border-indigo-600 shadow-lg shadow-indigo-200' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-300' }}">
                                {{ Carbon\Carbon::create()->month($i)->locale('ar')->monthName }}
                            </button>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

        @if(!$data)
            <div
                class="flex flex-col items-center justify-center py-20 bg-white border-2 border-gray-200 border-dashed rounded-3xl">
                <div
                    class="flex items-center justify-center w-20 h-20 mb-4 text-4xl rounded-full opacity-50 bg-gray-50 grayscale">
                    📊</div>
                <p class="text-lg font-bold text-gray-500">لا توجد بيانات لهذا الشهر</p>
                <p class="mt-1 text-sm text-gray-400">لم يتم تسجيل أي اجتماعات مكتملة في هذا التاريخ</p>
            </div>
        @else
            <!-- Summary Cards -->
            <div class="grid grid-cols-2 gap-4 mb-8 md:grid-cols-4">
                <div
                    class="flex flex-col items-center justify-center p-6 text-center bg-white border border-gray-100 shadow-sm rounded-3xl">
                    <span class="mb-2 text-xs font-bold tracking-wider text-gray-400 uppercase">اجتماعات الشهر</span>
                    <span class="text-3xl font-black text-indigo-600">{{ $data['meetings_count'] }}</span>
                </div>
                <div
                    class="flex flex-col items-center justify-center p-6 text-center bg-white border border-gray-100 shadow-sm rounded-3xl">
                    <span class="mb-2 text-xs font-bold tracking-wider text-gray-400 uppercase">عدد المخدومين</span>
                    <span class="text-3xl font-black text-purple-600">{{ count($data['members_stats']) }}</span>
                </div>

            </div>

            <!-- Members List Grid -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($data['members_stats'] as $stat)
                    <div
                        class="overflow-hidden transition-shadow bg-white border border-gray-200 shadow-sm rounded-3xl hover:shadow-md">

                        <!-- Header -->
                        <a href="{{ route('member.stats', $stat['id']) }}"
                            class="flex items-center justify-between p-4 border-b border-gray-50 bg-gray-50/50">
                            <!-- Link to Member Page -->
                            <div class="flex items-center gap-2 group">
                                <div
                                    class="flex items-center justify-center w-8 h-8 text-xs font-bold text-gray-500 transition-colors bg-white border border-gray-100 rounded-full shadow-sm group-hover:border-indigo-300 group-hover:text-indigo-600">
                                    {{ mb_substr($stat['name'], 0, 1) }}
                                </div>
                                <div>
                                    <h3
                                        class="text-sm font-bold text-gray-800 transition-colors group-hover:text-indigo-700 group-hover:underline">
                                        {{ $stat['name'] }}
                                    </h3>
                                    @if(!$stat['is_active']) <span class="text-[9px] text-red-500 block">غير نشط</span> @endif
                                </div>
                            </div>
                            <span
                                class="px-2 py-1 rounded-md text-xs font-bold {{ $stat['total_average'] >= 50 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $stat['total_average'] }}%</span>
                        </a>

                        <!-- Grid Stats -->
                        <div class="grid grid-cols-4 gap-3 p-4 text-center">
                            <!-- Row 1 -->
                            <div class="p-2 border border-blue-100 bg-blue-50 rounded-2xl">
                                <div class="text-[10px] text-gray-500 font-bold mb-1">حضور</div>
                                <div class="text-sm font-black text-blue-700">{{ $stat['attendance'] }}%</div>
                            </div>
                            <div class="p-2 border border-purple-100 bg-purple-50 rounded-2xl">
                                <div class="text-[10px] text-gray-500 font-bold mb-1">نوتة</div>
                                <div class="text-sm font-black text-purple-700">{{ $stat['note'] }}%</div>
                            </div>
                            <div class="p-2 border border-orange-100 bg-orange-50 rounded-2xl">
                                <div class="text-[10px] text-gray-500 font-bold mb-1">قداس</div>
                                <div class="text-sm font-black text-orange-700">{{ $stat['mass'] }}%</div>
                            </div>
                            <div class="p-2 border border-pink-100 bg-pink-50 rounded-2xl">
                                <div class="text-[10px] text-gray-500 font-bold mb-1">تدريب تلمذة</div>
                                <div class="text-sm font-black text-pink-700">{{ $stat['training'] }}%</div>
                            </div>

                            <div class="h-px col-span-4 my-2 bg-gray-100"></div>

                            <!-- Row 2 -->
                            <div class="col-span-1">
                                <div class="text-[10px] text-gray-400 font-bold">مشاركة خلوة</div>
                                <div class="text-sm font-bold text-gray-700">{{ $stat['kholwa'] }}%</div>
                            </div>
                            <div class="col-span-1">
                                <div class="text-[10px] text-gray-400 font-bold">تسبحة</div>
                                <div class="text-sm font-bold text-gray-700">{{ $stat['tasbeha'] }}%</div>
                            </div>
                            <div class="col-span-1">
                                <div class="text-[10px] text-gray-400 font-bold">اجتماع خدام</div>
                                <div class="text-sm font-bold text-gray-700">{{ $stat['servants'] }}%</div>
                            </div>
                            <div class="col-span-1">
                                <div class="text-[10px] text-gray-400 font-bold">مذبح عائلى</div>
                                <div class="text-sm font-bold text-gray-700">{{ $stat['altar'] }}%</div>
                            </div>

                            <div class="col-span-2 pt-2 mt-2 border-t border-gray-50">
                                <div class="text-[10px] text-gray-400 font-bold">قراءة</div>
                                <div class="text-sm font-bold text-gray-700">{{ $stat['reading'] }}%</div>
                            </div>
                            <div class="col-span-2 pt-2 mt-2 border-t border-gray-50">
                                <div class="text-[10px] text-gray-400 font-bold">خلوة أسبوعية</div>
                                <div class="text-sm font-bold text-gray-700">{{ $stat['weekly_kholwa'] }}%</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>