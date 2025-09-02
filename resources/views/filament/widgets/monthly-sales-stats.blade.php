<x-filament-widgets::widget>
    <x-filament::section>
        <h2 class="text-xl font-bold mb-4">
            Sales Total {{ $month }}/{{ $year }}
        </h2>

        <div class="grid grid-cols-2 gap-4">
            {{-- Total bulan ini --}}
            <x-filament::card class="text-center">
                <div class="text-lg font-semibold">Sales Total</div>
                <div class="text-2xl font-bold">
                   IDR {{ number_format($this->getCurrentTotal(), 0, ',', '.') }} 
                </div>
                <div class="text-sm text-gray-500">
                    Last Month: 
                    <span class="inline-flex items-center space-x-1 {{ $this->getPercentageDifference() >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        <span>
                            {{ $this->getPercentageDifference() >= 0 ? '+' : '' }}{{ $this->getPercentageDifference() }}%
                        </span>
                
                        @if ($this->getPercentageDifference() >= 0)
                            <!-- Icon naik (trending up) -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                        @else
                            <!-- Icon turun (trending down) -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        @endif
                    </span>
                </div>
                
            </x-filament::card>

            {{-- Total bulan sebelumnya --}}
            <x-filament::card class="text-center">
                <div class="text-lg font-semibold">Previous Month</div>
                <div class="text-2xl font-bold">
                IDR {{ number_format($this->getPreviousTotal(), 0, ',', '.') }}
                </div>
            </x-filament::card>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
