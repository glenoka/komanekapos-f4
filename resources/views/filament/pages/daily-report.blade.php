<x-filament::page>

{{ $this->form }}
  
   
    @livewire(\App\Filament\Widgets\MonthlySalesStats::class, [
    'month' => $selectedMonth ?? now()->month,
    'year' => $selectedYear ?? now()->year,
], key($selectedMonth.'-'.$selectedYear))
   
    {{-- Kalender Penjualan --}}
    <x-filament::section>
        <x-slot name="heading">
            <h2 class="text-2xl font-bold mb-6">📅 Sales Calendar - {{ \Carbon\Carbon::createFromDate($selectedYear ?? now()->year, $selectedMonth ?? now()->month)->translatedFormat('F Y') }}
            </h2>
        </x-slot>
    
         {{-- Header Hari --}}
    <div class="grid grid-cols-7 gap-2 text-center text-sm font-semibold text-gray-600 mb-2">
        <div>Mon</div>
        <div>Tue</div>
        <div>Wed</div>
        <div>Thu</div>
        <div>Fri</div>
        <div class="text-blue-600">Sat</div>
        <div class="text-red-600">Sun</div>
    </div>

    {{-- Grid Kalender --}}
    <div class="grid grid-cols-7 gap-2">
        @foreach ($this->calendarData as $week)
            @foreach ($week as $day)
                @if ($day)
                    @php
                        $hasData = $day['total'] > 0;
                    @endphp

                    <div
                        class="rounded-md p-3 border text-left shadow-sm transition-all duration-200
                            {{ $hasData ? 'bg-green-50 border-green-200 hover:bg-green-100' : 'bg-gray-50 border-gray-200 hover:bg-gray-100' }}"
                    >
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-gray-800">
                                {{ $day['date']->format('d') }}
                            </span>

                            @if ($hasData)
                                <span class="text-[10px] px-2 py-0.5 bg-green-600 text-white rounded-full">
                                    ✔
                                </span>
                            @endif
                        </div>

                        <div class="text-xs text-gray-700 leading-tight">
                            <span class="block">Total: Rp {{ number_format($day['total'], 0, ',', '.') }}</span>
                            <span class="block text-[11px] text-gray-500">Tax: Rp {{ number_format($day['tax'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                @else
                    {{-- Cell kosong --}}
                    <div class="p-3"></div>
                @endif
            @endforeach
        @endforeach
    </div>
    </x-filament::section>
   
   
</x-filament::page>
