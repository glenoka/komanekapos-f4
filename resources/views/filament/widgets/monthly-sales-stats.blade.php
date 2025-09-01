<x-filament-widgets::widget>
    <x-filament::section>
        <h2 class="text-xl font-bold mb-4">
            Penjualan bulan {{ $month }}/{{ $year }}
        </h2>

        <div class="grid grid-cols-2 gap-4">
            {{-- Total bulan ini --}}
            <x-filament::card class="text-center">
                <div class="text-lg font-semibold">Total Penjualan</div>
                <div class="text-2xl font-bold">
                    Rp {{ number_format($this->getCurrentTotal(), 0, ',', '.') }}
                </div>
                <div class="text-sm text-gray-500">
                    Dibanding bulan lalu: 
                    <span class="{{ $this->getPercentageDifference() >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $this->getPercentageDifference() >= 0 ? '+' : '' }}{{ $this->getPercentageDifference() }}%
                    </span>
                </div>
            </x-filament::card>

            {{-- Total bulan sebelumnya --}}
            <x-filament::card class="text-center">
                <div class="text-lg font-semibold">Bulan Sebelumnya</div>
                <div class="text-2xl font-bold">
                    Rp {{ number_format($this->getPreviousTotal(), 0, ',', '.') }}
                </div>
            </x-filament::card>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
