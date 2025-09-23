<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
   
    <div class="md:col-span-2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <!-- Bagian Kategori + Search -->
       
        <div class="flex gap-4 items-center justify-between mb-4">


            <div class="flex gap-4 overflow-x-auto pb-3 px-5 overflow-y-hidden flex-1">
                @foreach ($categories as $category)
                    <div class="relative">
                        <x-filament::button class="whitespace-nowrap mb-2 transition-all duration-200 hover:scale-105"
                            wire:click="$set('activeCategory', {{ $category->id }})" :color="$activeCategory === $category->id ? 'primary' : 'info'" size="sm">
                            {{ $category->name }}
                        </x-filament::button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Search Input -->
        <div class="flex-shrink-0 mb-3">
            <x-filament::input.wrapper>
                <x-filament::input type="search" wire:model.live.debounce.500ms="search" placeholder="Cari produk..."
                    icon="heroicon-o-magnifying-glass" class="h-9 mb-3 transition-all duration-200" />
            </x-filament::input.wrapper>
        </div>
        <!-- Daftar Produk -->
        <div class="flex-grow mt-3">
    <div class="flex-grow mt-3">
        <!-- Responsive grid: 2 kolom HP, 3 kolom tablet, 5 kolom desktop -->
        <div class="grid gap-2 p-1" style="grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));">
            @foreach ($products as $product)
                <x-filament::section
                    class="!p-2 !m-0 cursor-pointer transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:scale-105 group border border-transparent hover:border-primary-200 dark:hover:border-primary-700"
                    wire:click="addToOrder({{ $product->id }})" style="min-width: 140px; max-width: 220px;">
                    <div class="space-y-2">
                        <!-- Product details -->
                        <div class="text-center space-y-2">
                            <h3
                                class="text-xs font-medium text-gray-900 dark:text-white line-clamp-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors duration-300 leading-tight min-h-[2.5rem] flex items-center justify-center">
                                {{ $product->name ?? 'Nama Produk' }}
                            </h3>

                            <div class="w-full">
                                <x-filament::badge color="success" size="sm"
                                    class="font-semibold group-hover:scale-105 transition-transform duration-200 whitespace-nowrap text-xs px-2 py-1 inline-block w-auto max-w-full overflow-visible">
                                    <span class="block text-center">Rp {{ number_format($product->price ?? 10000, 0, ',', '.') }}</span>
                                </x-filament::badge>
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            @endforeach
        </div>
    </div>

    <!-- Pagination -->
    <div class="py-4">
        <x-filament::pagination :paginator="$products" :page-options="[5, 10, 20, 50, 100]" extreme-links :current-page-option-property="$perPage" />
    </div>
</div>




    </div>

    <!-- Sidebar Cart -->
    <div class="md:col-span-1 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <!-- Cart Header -->
        <div class="flex justify-between items-center py-4 border-b border-gray-200 dark:border-gray-700 mb-4">
            @if (count($order_items) > 0)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cart</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ count($order_items) }} item</p>
                </div>
            @else
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cart</h3>
            @endif

            <div class="flex gap-3"> <!-- Menggunakan gap-3 untuk jarak yang optimal -->
                @if (count($order_items) > 0)
                    <x-filament::button wire:click="clearOrder" color="danger" size="sm"
                        class="transition-all duration-200 hover:scale-105">
                        <span>Clear</span>
                    </x-filament::button>
                @endif
                
                <x-filament::button badge-color="danger" color="gray" size="sm" wire:click="loadHoldBillList()">
                    <x-slot name="badge">{{ $this->countBillHold }}</x-slot>
                    <span>Hold Bill</span>
                </x-filament::button>
                
                <x-filament::button badge-color="danger" color="gray" size="sm" wire:click="downloadReceipt()">
                    <x-slot name="badge">{{ $this->countBillHold }}</x-slot>
                    <span>Print</span>
                </x-filament::button>
                {{-- Modal List --}}
                
            </div>
        </div>

        <!-- Total Section -->
        @if (count($order_items) > 0)
            <div
                class="bg-primary-50 dark:bg-primary-900/20 p-4 rounded-lg mb-4 border border-primary-200 dark:border-primary-700">
                <div class="text-center">
                    <p class="text-sm text-primary-600 dark:text-primary-400 font-medium">Total Payment</p>
                    <h3 class="text-xl font-bold text-primary-700 dark:text-primary-300">
                        Rp {{ number_format($this->calculateTotal(), 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        @endif

        <!-- Cart Items -->
        <div class="space-y-3 max-h-80 overflow-y-auto">
            @forelse ($order_items as $index => $item)
                <div
                    class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 transition-all duration-200 hover:shadow-md">
                    <!-- Item Header with Quantity Controls Inline -->
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex-1 pr-2 flex items-center gap-2">
                            {{-- Tombol Action --}}
                            {{ ($this->discountItem)(['product_id' => $item['product_id'], 'item_index' => $index]) }}

                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $item['name'] }}
                                </h3>
                                <p class="text-success-600 dark:text-success-300 text-xs font-medium">
                                    Rp {{ number_format($item['unit_price'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <!-- Quantity Controls Inline -->
                        <div class="flex items-center gap-2">
                            <x-filament::button 
            color="gray" 
            size="sm"
            wire:click="decreaseQty({{ $index }})">
            -
        </x-filament::button>
        <x-filament::input.wrapper class='w-16'>
    <x-filament::input
    type="number"
    wire:model.live="order_items.{{ $index }}.quantity"
    wire:change="updateSubtotal({{ $index }})"
    class="w-16 text-center"
    />
</x-filament::input.wrapper >
        

    <x-filament::button 
    color="gray" 
    size="sm"
    wire:click="increaseQty({{ $index }})">
    +
</x-filament::button>
                        </div>
                    </div>

                    <!-- Subtotal -->
                    <div class="mt-2">
                        <p class="text-xs text-gray-600 dark:text-gray-300 font-medium">
                            Subtotal:
                            @if ((float) $item['discount'] > 0)
                                <span class="line-through text-gray-400 dark:text-gray-500 mr-2">
                                    Rp {{ number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') }}
                                </span>
                                <span class="text-primary-600 dark:text-primary-400 font-semibold">
                                    Rp {{ number_format($item['final_price'], 0, ',', '.') }}
                                </span>
                            @else
                                Rp {{ number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') }}
                            @endif
                        </p>
                    </div>
                </div>
            @empty
                <!-- Empty Cart State -->
                <div class="text-center py-8">
                    <div
                        class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                        <x-heroicon-o-shopping-cart class="w-8 h-8 text-gray-400" />
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Your cart is empty</p>
                    <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">Add items to get started</p>
                </div>
            @endforelse
        </div>

        <!-- Form and Action Buttons -->
        @if (count($order_items) > 0)
            <form wire:submit="processPayment">
                {{ $this->orderForm}}

                <div class="flex flex-col gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <!-- Hold Bill & Process Payment Side by Side -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Hold Bill Button -->
                        <x-filament::button wire:click="mountAction('actionHoldBill')" color="gray"
                            icon="heroicon-o-clock" size="lg"
                            class="flex-1 justify-center transition-all duration-200 hover:scale-105"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>Hold Bill</span>
                            <span wire:loading class="flex items-center">
                                <x-filament::loading-indicator class="w-4 h-4 mr-2" />
                                Processing...
                            </span>
                        </x-filament::button>
                        
                        <!-- Process Payment Modal Trigger -->
                        <x-filament::modal width="xl">
                            <x-slot name="trigger">
                                <x-filament::button color="primary" icon="heroicon-o-credit-card" size="lg"
                                    class="flex-1 justify-center font-semibold transition-all duration-200 hover:scale-105 shadow-lg"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>Process Payment</span>
                                    <span wire:loading class="flex items-center">
                                        <x-filament::loading-indicator class="w-4 h-4 text-white mr-2" />
                                        Processing...
                                    </span>
                                </x-filament::button>
                            </x-slot>
                            <div
                                class="bg-primary-50 dark:bg-primary-900/20 p-4 rounded-lg mb-4 border border-primary-200 dark:border-primary-700">
                                <div class="text-center">
                                    <p class="text-sm text-primary-600 dark:text-primary-400 font-medium">Total Payment
                                    </p>
                                    <h3 class="text-xl font-bold text-primary-700 dark:text-primary-300">
                                        Rp {{ number_format($this->calculateTotal(), 0, ',', '.') }}
                                    </h3>
                                </div>
                            </div>
                            <div class="w-full bg-white rounded-lg shadow-sm">
                                <!-- Cart Header with Toggle -->
                                <div
                                    class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                                    <h2 class="text-lg font-semibold">Your Order</h2>
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-500 mr-2">{{ count($order_items) }}
                                            items</span>

                                    </div>
                                </div>

                                <!-- Cart Items - Simplified -->
                                <div x-data="{ collapsed: false }" x-show="!collapsed" class="divide-y divide-gray-100">
                                    @foreach ($order_items as $index => $item)
                                        <div class="px-4 py-3 hover:bg-gray-50">
                                            <div class="flex justify-between items-baseline">
                                                <div class="flex-1">
                                                    <span class="text-gray-500 mr-2">{{ $index + 1 }}.</span>
                                                    <span class="font-medium">{{ $item['name'] }}</span>
                                                    <span class="text-gray-500 ml-2">x {{ $item['quantity'] }}</span>
                                                </div>
                                                <div class="text-right">

                                                    <span class="font-medium">Rp
                                                        {{ number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Order Summary - Always Visible -->
                                <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                                    <div class="grid grid-cols-2 gap-y-1 text-sm">
                                        <div class="text-gray-600">Subtotal:</div>
                                        <div class="text-right font-medium">Rp
                                            {{ number_format($this->calculateSubtotal(), 0, ',', '.') }}</div>

                                        <div class="text-gray-600">Tax ({{ $tax }}%):</div>
                                        <div class="text-right font-medium">Rp
                                            {{ number_format($this->calculateTax(), 0, ',', '.') }}</div>

                                        <div class="text-gray-600">Discount:</div>
                                        <div class="text-right font-medium text-green-600">- Rp
                                            {{ number_format($this->discount_amount, 0, ',', '.') }}</div>

                                        <div class="text-gray-800 font-semibold mt-1">Total:</div>
                                        <div class="text-right font-bold text-lg">Rp
                                            {{ number_format($this->calculateTotal(), 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>


                            <form wire:submit="processPayment">
                                {{ $this->paymentForm }}
                                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                                    <x-filament::button type="submit" size="lg" color="primary"
                                        icon="heroicon-o-check-circle" class="flex-1 justify-center"
                                        wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="processPayment">Process Payment</span>
                                        <span wire:loading wire:target="processPayment" class="flex items-center">
                                            <x-filament::loading-indicator class="w-4 h-4 mr-2" />
                                            Processing...
                                        </span>
                                    </x-filament::button>

                                    <x-filament::button type="button" size="lg" color="gray"
                                        icon="heroicon-o-x-circle" class="flex-1 justify-center"
                                        wire:click="cancelPayment" wire:loading.attr="disabled">
                                        Cancel
                                    </x-filament::button>
                                </div>
                            </form>
                        </x-filament::modal>
                        <!-- Payment Button -->

                    </div>
            </form>
        @endif
    </div>
    <x-filament-actions::modals />

    <x-filament::modal id="ModalListBill" width="4xl"> <!-- Lebarkan modal -->
    @if ($holdBillList->isEmpty())
        <x-filament::section>
            <div class="flex flex-col items-center justify-center py-12 text-gray-500 text-sm">
                <x-heroicon-o-inbox class="w-12 h-12 mb-4"/>
                No bills are currently on hold.
            </div>
        </x-filament::section>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4"> <!-- Grid 2 kolom -->
            @foreach ($holdBillList as $bill)
                <x-filament::section class="h-full"> <!-- Tambahkan h-full -->
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-lg">
                                {{ $bill->customer->name ?? 'No Customer' }}
                            </h3>
                            <p class="text-xs text-gray-400">
                                {{ $bill->sale_date }} {{ $bill->sale_time }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold">Room {{ $bill->room_number ?? '-' }}</p>
                            <p class="text-xs text-gray-400">{{ $bill->created_at->format('H:i:s') }}</p>
                        </div>
                    </div>

                    <div class="mt-4 text-sm space-y-2">
                        <p><strong>Type Order:</strong> 
                            <span class="inline-block bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-xs ml-1">
                                {{ $bill->order_type ?? '-' }}
                            </span>
                        </p>
                        <p><strong>Table:</strong> {{ $bill->table_no ?? '-' }}</p>
                        <p><strong>Total:</strong> 
                            <span class="text-green-600 font-semibold">
                                Rp. {{ number_format($bill->total_amount, 0, ',', '.') }}
                            </span>
                        </p>
                        <p><strong>Items:</strong> {{ $bill->detailSales->count() }}</p>
                    </div>

                    <div class="flex gap-3 mt-4">
                        <x-filament::button color="success" size="sm" class="flex-1 justify-center"
                            wire:click="resumeBill('{{ $bill->slug }}')">
                            <x-heroicon-o-play class="w-4 h-4 mr-1"/> Resume
                        </x-filament::button>

                        <x-filament::button color="danger" size="sm" class="flex-1 justify-center"
                            wire:click="deleteBill('{{ $bill->slug }}')">
                            <x-heroicon-o-trash class="w-4 h-4 mr-1"/> Delete
                        </x-filament::button>
                    </div>
                </x-filament::section>
            @endforeach
        </div>
    @endif
</x-filament::modal>

</div>
