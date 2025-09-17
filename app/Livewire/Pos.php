<?php

namespace App\Livewire;


use Filament\Forms;
use App\Models\Sales;
use App\Models\Product;
use Filament\Schemas\Components\Utilities\Get;

use Filament\Forms\Set;
use Livewire\Component;
use App\Models\Category;
use App\Models\Printers;
use App\Models\Customer;
use App\Models\PrinterUser;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Mike42\Escpos\Printer;


use App\Models\SalesDetail;

use Filament\Actions\Action;
use Livewire\WithPagination;
use Mike42\Escpos\EscposImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\Column;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Placeholder;

use Illuminate\Validation\ValidationException;
use Filament\Schemas\Schema;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Concerns\InteractsWithActions;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class Pos extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithActions;
    // use WithPagination;

    public $activeCategory = 0; // Default category
    public $search = '';
    public int | string $perPage = 10;
    public $sub_total;
    public $order_items = [];

    public $tax = 11;
    public $tax_amount;
    public $discount = 0;
    public $discount_amount;
    public $grand_total;


    public $order_type;
    public $customer_id;
    public $number_table;
    public $activity;

    // Property untuk paymentForm
    public $sales_type;
    public $payment_method;
    public $notes;

    public $holdBillList;
    public $countBillHold = 0;

    public $bill_slug; //untuk slug bill yang di resume 

    public $printerStatus;



    public function mount(): void
    {
        //Check Printer 


        if (session()->has('orderItems')) {
            $this->order_items = session('orderItems');
        }


        if (session()->has('orderItems')) {
            $this->order_items = session('orderItems');
        }
        //dd($this->order_items);

        $this->holdBillList = Sales::where('status', 'pending')
            ->with('detailSales')
            ->get();
        $this->countBillHold = count($this->holdBillList);
    }


    protected function getForms(): array
    {
        return [
            'paymentForm',
            'orderForm'
        ];
    }
    // protected function getForms(): array
    // {
    //     return [
    //         'paymentForm' => $this->makeForm()
    //             ->schema($this->getPaymentFormSchema()),

    //         'orderForm' => $this->makeForm()
    //             ->schema($this->getOrderFormSchema()),
    //     ];
    // }


    public function updatedSearch($value)
    {
        if (!empty($value)) {
            $this->activeCategory = 0; // Otomatis reset kategori saat mencari
        }
        $this->resetPage();
    }

    public function updatedActiveCategory()
    {
        $this->resetPage();
    }


    public function clearOrder()
    {
        $this->order_items = [];
        session()->forget('orderItems');
        $this->grand_total = 0;
        $this->discount = 0;
        $this->discount_amount = 0;
        $this->sub_total = 0;
        $this->tax_amount = 0;

        $this->order_type = '';
        $this->customer_id = '';
        $this->number_table = '';
        $this->activity = '';

        $this->bill_slug = ''; //jika sebelumnya ada resume bill agar terhapus

        Notification::make()
            ->title('Cart cleared successfully')
            ->success()
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query()->limit(5)) // Menambahkan limit(5)
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->sortable(),
                TextColumn::make('price')->sortable(),
            ]);
    }
    protected function orderForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Summary')
                    ->schema([
                        // Customer and Order Info
                        Grid::make(2)
                            ->schema([
                                Select::make('order_type')
                                    ->label('Order Type')
                                    ->required()
                                    ->live()
                                    ->options([
                                        'dine_in' => 'Dine In',
                                        'room_service' => 'Room Service',
                                        'takeaway' => 'Take Away',
                                        'other' => 'Other'
                                    ])
                                    ->columnSpan(1),

                                Select::make('number_table')
                                    ->label('Table Number')
                                    ->options([
                                        '1' => '1',
                                        '2' => '2',
                                        '3' => '3',
                                        '4' => '4',
                                    ])
                                    ->visible(fn(Get $get) => $get('order_type') === 'dine_in')
                                    ->required(fn(Get $get) => $get('order_type') === 'dine_in')
                                    ->columnSpan(1),
                            ]),

                        Select::make('activity')
                            ->label('Activity')
                            ->options([
                                'breakfast' => 'Breakfast',
                                'lunch' => 'Lunch',
                                'dinner' => 'Dinner',
                                'entertainment' => 'Entertainment',
                                'officer' => 'Officer',
                                'room_service' => 'Room service',
                                'dinner_inclusive' => 'Dinner inclusive',
                                'lunch_inclusive' => 'Lunch inclusive',
                                'drink' => 'Drink',
                                'candle_light_dinner' => 'Candle light dinner',
                                'supper' => 'Supper',
                                'red_light_special_dinner' => 'Red light special dinner',
                                'afternoon_tea' => 'Afternoon tea',
                            ]),

                        Select::make('customer_id')
                            ->label('Customer')
                            ->options(Customer::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                    ]),

                // Payment Summary Section
                Section::make('Payment Summary')
                    ->schema([
                        TextInput::make('sub_total')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->columnSpan(1),

                        TextInput::make('discount')
                            ->label('Discount Amount')
                            ->numeric()
                            ->suffix('%')
                            ->live()
                            ->default(0)
                            ->hint(fn() => '- Rp. ' . $this->discount_amount)
                            ->hintColor('success')
                            ->hintIcon('heroicon-s-tag')
                            ->columnSpan(1),

                        TextInput::make('tax')
                            ->label('Tax Amount')
                            ->numeric()
                            ->suffix('%')
                            ->readOnly()
                            ->live()
                            ->hint(fn() => '+ Rp. ' . $this->tax_amount)
                            ->hintColor('danger')
                            ->hintIcon('heroicon-s-receipt-percent')
                            ->columnSpan(1),
                    ]),

                // Grand Total
                TextInput::make('grand_total')
                    ->label('Grand Total')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->default(fn() => $this->calculateTotal())
                    ->extraAttributes(['class' => 'font-bold text-lg'])
                    ->columnSpanFull(),
            ]);
    }

    public function paymentForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('bill_slug')
                    ->default(fn() => $this->bill_slug),

                Section::make('Transaction Details')
                    ->description('Configure transaction type and payment method')
                    ->schema([
                        Select::make('sales_type')
                            ->label('Sales Type')
                            ->required()
                            ->live()
                            ->searchable()
                            ->preload()
                            ->placeholder('Select sales type...')
                            ->options([
                                'regular' => 'Regular Sale',
                                'complimentary' => 'Complimentary',
                                'owner_guest' => 'Owner Guest',
                                'staff_meal' => 'Staff Meal',
                                'vip_guest' => 'VIP Guest',
                                'business_entertainment' => 'Business Entertainment',
                                'banquet/wedding' => 'Banquet/Wedding',
                            ])
                            ->native(false)
                            ->suffixIcon('heroicon-o-tag')
                            ->columnSpanFull(),

                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Select payment method...')
                            ->options([
                                'cash' => 'Cash',
                                'card' => 'Credit/Debit Card',
                                'room_charge' => 'Room Charge',
                                'complimentary' => 'Complimentary',
                                'qris' => 'QRIS',
                            ])
                            ->native(false)
                            ->suffixIcon('heroicon-o-credit-card')
                            ->reactive()
                            ->afterStateUpdated(
                                fn(callable $set, $state) =>
                                $state === 'complimentary'
                                    ? $set('sales_type', 'complimentary')
                                    : null
                            )
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Section::make('Additional Information')
                    ->description('Optional notes and comments')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->placeholder('Add any special notes or instructions...')
                            ->rows(3)
                            ->required(fn(Get $get) => $get('sales_type') === 'complimentary')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->persistCollapsed()
                    ->collapsed(),
            ]);
    }
    public function increaseQty($index)
{
    $this->order_items[$index]['quantity']++;
    $this->updateSubtotal($index);
}

public function decreaseQty($index)
{
    if ($this->order_items[$index]['quantity'] > 1) {
        $this->order_items[$index]['quantity']--;
        $this->updateSubtotal($index);
    }
}

public function updateSubtotal($index)
{
    $qty = $this->order_items[$index]['quantity'];
    $unit_price = $this->order_items[$index]['unit_price'];

    // hitung ulang price
    $this->order_items[$index]['price'] = $qty * $unit_price;

    // untuk sekarang discount_amount masih 0
    $discount_amount = $this->order_items[$index]['discount_amount'] ?? 0;

    // hitung final price
    $this->order_items[$index]['final_price'] = $this->order_items[$index]['price'] - $discount_amount;
}

    // public function increaseQuantity($productId)
    // {
    //     $product = Product::find($productId);

    //     foreach ($this->order_items as $key => $item) {
    //         if ($item['product_id'] == $product->id) {
    //             $this->order_items[$key]['quantity']++;
    //             $this->order_items[$key]['price'] = $this->order_items[$key]['unit_price'] * $this->order_items[$key]['quantity'];
    //             $this->order_items[$key]['final_price'] = $this->order_items[$key]['price'];
    //             $this->recalculateItemPrice($key, $this->order_items[$key]['discount']);
    //         }
    //     }
    //     session()->put('orderItems', $this->order_items);
    // }

    // public function decreaseQuantity($productId)
    // {
    //     $product = Product::find($productId);

    //     foreach ($this->order_items as $key => $item) {
    //         if ($item['product_id'] == $product->id) {
    //             if ($this->order_items[$key]['quantity'] > 1) {

    //                 $this->order_items[$key]['quantity']--;
    //                 $this->order_items[$key]['price'] = $this->order_items[$key]['unit_price'] * $this->order_items[$key]['quantity'];
    //                 $this->order_items[$key]['final_price'] = $this->order_items[$key]['price'];
    //                 $this->recalculateItemPrice($key, $this->order_items[$key]['discount']);
    //             } else {
    //                 unset($this->order_items[$key]);
    //                 $this->order_items = array_values($this->order_items);
    //             }
    //             break;
    //         }
    //     }
    //     session()->put('orderItems', $this->order_items);
    // }
    // Helper methods
    public function calculateSubtotal()
    {
        $total = 0;
        foreach ($this->order_items as $item) {
            $total += $item['price'];
        }
        $this->sub_total = $total; //sebelum pajak 
        return $total;
    }

    public function calculateTax()
    {
        return $this->tax_amount = $this->calculateSubtotal() * ($this->tax / 100);
    }

    public function calculateTotal()
    {
        $subtotal = $this->calculateSubtotal();
        $tax = $this->calculateTax();
        if ($this->discount > 0) {
            $this->discount_amount = $this->sub_total * $this->discount / 100;
        } else {
            $this->discount_amount = 0;
        }
        return $this->grand_total = $subtotal + $tax - $this->discount_amount;
    }
    public function addToOrder($productId)
    {
        $product = Product::find($productId);
        $existingItemkey = null;

        foreach ($this->order_items as $key => $item) {
            if ($item['product_id'] == $product->id) {
                $existingItemkey = $key;
                break;
            }
        }

        if ($existingItemkey !== null) {
            $this->order_items[$existingItemkey]['quantity']++;

            $this->order_items[$existingItemkey]['price'] = $this->order_items[$existingItemkey]['unit_price'] * $this->order_items[$existingItemkey]['quantity'];
        } else {
            $this->order_items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'unit_price' => $product->price,
                'price' => $product->price, //karena qty masih 1 
                'discount' => 0, //karena belum di isi 
                'discount_amount' => 0,
                'final_price' => $product->price, //karena belum ada diskon
                'quantity' => 1,
            ];
        }

        session()->put('orderItems', $this->order_items);
        Notification::make('addToOrder_' . now()->timestamp)
            ->title('Add item Success')
            ->success()
            ->send();
    }

    public function discountItem(): Action
    {

        return Action::make('discountItem')
            ->label('Beri Diskon')
            ->icon('heroicon-o-receipt-percent')
            ->color('info')
            ->size('xs')
            ->iconButton()
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Diskon')
            ->modalDescription('Yakin ingin memberikan diskon pada produk ini?')
            ->modalSubmitActionLabel('Ya, Beri Diskon')
            ->tooltip('Beri Diskon')
            ->schema([
                TextInput::make('discount_percentage')
                    ->label('Diskon (%)')
                    ->numeric()
                    ->required()
                    ->suffix('%')
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.01)
                    ->default(10)
                    ->helperText('Masukkan persentase diskon (0-100)'),
            ])
            ->action(function (array $data, array $arguments) {
                // $arguments berisi parameter yang dikirim
                $productId = $arguments['product_id'];
                $itemIndex = $arguments['item_index'] ?? null;

                $this->recalculateItemPrice($itemIndex, $data['discount_percentage']);
            });
    }





    //check untuk discount
    public function recalculateItemPrice($itemIndex, $discount)
    {
        $item = $this->order_items[$itemIndex];


        $discountAmount = $item['price'] * ($discount / 100);

        $this->order_items[$itemIndex]['discount_amount'] = $discountAmount;
        $this->order_items[$itemIndex]['discount'] = $discount;
        $this->order_items[$itemIndex]['final_price'] = $item['price'] - $discountAmount;
        session()->put('orderItems', $this->order_items);
    }
    public function loadOrderItem($orderItems)
    {
        $this->order_items = $orderItems;
        session()->put('orderItems', $this->order_items);
    }
    public function processPayment()
    {



        // Validasi payment form
        $paymentData = $this->validate([
            'sales_type' => 'required',
            'payment_method' => 'required',
            'notes' => 'required_if:sales_type,complimentary'
        ]);

        // $dataTest=[
        //     'customer_id' => $this->customer_id,
        //     'sale_date' => now(),
        //     'table_no' => $this->number_table,
        //     'sales_type' => $this->sales_type,
        //     'order_type' => $this->order_type,
        //     'subtotal' => $this->sub_total,
        //     'tax_amount' => $this->tax_amount,
        //     'discount_amount' => $this->discount_amount,
        //     'total_amount' => $this->grand_total,
        //     'payment_method' => $this->payment_method,
        //     'total_items' => count($this->order_items),
        //     'status' => 'completed',
        //     'user_id' => Auth::user()->id,
        //     'notes' => $this->notes,
        // ];
        // dd($dataTest);
        DB::transaction(function () {
            $sales = Sales::updateOrCreate(
                ['slug' => $this->bill_slug],
                [
                    'customer_id' => $this->customer_id,
                    'sale_date' => now(),
                    'table_no' => $this->number_table,
                    'sales_type' => $this->sales_type,
                    'order_type' => $this->order_type,
                    'subtotal' => $this->sub_total,
                    'tax_amount' => $this->tax_amount,
                    'discount_amount' => $this->discount_amount,
                    'total_amount' => $this->grand_total,
                    'payment_method' => $this->payment_method,
                    'total_items' => count($this->order_items),
                    'status' => 'completed',
                    'user_id' => Auth::user()->id,
                    'notes' => $this->notes,
                ]
            );

            foreach ($this->order_items as $item) {

                $detailSales = SalesDetail::updateOrCreate([
                    'sale_id' => $sales->id,
                    'product_id' => $item['product_id'],
                ], [
                    'sale_id' => $sales->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'original_price' => $item['price'],
                    'discount' => $item['discount'],
                    'discount_amount' => $item['discount_amount'],
                    'total_price' => $item['final_price'], //yang sudah di kurangi diskon jika ada
                    'is_complimentary' => false,
                ]);
            }
            //print if printer is reachable

            if (session()->get('status_printer')) {
                $this->printOrderToLan($sales->id);
            }
        });
        Notification::make('payment')
            ->title('Paymanet Success')
            ->success()
            ->send();

        $this->order_items = [];
        $this->sub_total = 0;
        $this->tax_amount = 0;
        $this->discount_amount = 0;
        $this->grand_total = 0;

        $this->order_type = '';
        $this->customer_id = '';
        $this->number_table = '';
        $this->activity = '';

        $this->refreshCountBillHold();
        //forget dan kosongkan order_item
        $this->order_items = [];
        session()->forget('orderItems');

        $this->bill_slug = ''; //jika sebelumnya ada resume bill agar terhapus


    }
    public function actionHoldBill(): Action
    {
        return Action::make('holdBill')
            ->label('Hold Bill')
            ->icon('heroicon-o-receipt-percent')
            ->color('info')
            ->size('xs')
            ->iconButton()
            ->requiresConfirmation()
            ->modalHeading('Confirm Hold Bill')
            ->modalDescription('Are you sure you want to place this bill on hold?')
            ->modalSubmitActionLabel('Yes, Hold Bill')
            // NOTE: accepting Action $action here is essential
            ->action(function (Action $action) {
                try {
                    // Validate component properties (your current approach)
                    $this->validate([
                        'order_type'  => 'required',
                        'customer_id' => 'required',
                    ]);

                    // Your existing save/process logic
                    $this->processHoldbill();

                    Notification::make()
                        ->title('Bill Placed on Hold')
                        ->body('The selected bill has been successfully placed on hold. You can resume processing it at any time from the billing list.')
                        ->success()
                        ->send();
                } catch (ValidationException $e) {
                    // Show error notification and CLOSE the modal
                    Notification::make()
                        ->title('Missing Required Information')
                        ->body('Please make sure all required fields are filled before holding the bill.')
                        ->danger()
                        ->send();

                    // This closes the modal immediately
                    $action->cancel();

                    return;
                }
            });
    }




    public function processHoldbill()
    {
        $paymentData = $this->validate([
            'order_type' => 'required',
            'customer_id' => 'required',
        ]);



        DB::transaction(function () {
            $sales = Sales::updateOrCreate(
                ['slug' => $this->bill_slug],
                [
                    'customer_id'     => $this->customer_id,
                    'sale_date'       => now(),
                    'table_no'        => $this->number_table,
                    'sales_type'      => $this->sales_type,
                    'order_type'      => $this->order_type,
                    'subtotal'        => $this->sub_total,
                    'tax_amount'      => $this->tax_amount,
                    'discount_amount' => $this->discount_amount,
                    'total_amount'    => $this->grand_total,
                    'payment_method'  => $this->payment_method,
                    'total_items'     => count($this->order_items),
                    'status'          => 'pending',
                    'user_id'         => Auth::id(),
                    'notes'           => $this->notes,
                ]
            );
            //hapus data yang tidak ada di order_item
            // Ambil semua product_id dari request
            $newProductIds = collect($this->order_items)->pluck('product_id')->toArray();

            // Hapus item lama yang tidak ada di request
            SalesDetail::where('sale_id', $sales->id)
                ->whereNotIn('product_id', $newProductIds)
                ->delete();



            foreach ($this->order_items as $item) {
                SalesDetail::updateOrCreate(
                    [
                        'sale_id'   => $sales->id,
                        'product_id' => $item['product_id'],
                    ],
                    [
                        'product_name'      => $item['name'],
                        'quantity'          => $item['quantity'],
                        'unit_price'        => $item['unit_price'],
                        'original_price'    => $item['price'],
                        'discount_amount'   => $item['discount_amount'],
                        'total_price'       => $item['final_price'], // harga setelah diskon
                        'is_complimentary'  => false,
                    ]
                );
            }
        });


        $this->order_items = [];
        session()->forget('orderItems');
        $this->grand_total = 0;
        $this->discount = 0;
        $this->discount_amount = 0;
        $this->sub_total = 0;
        $this->tax_amount = 0;
        $this->order_type = '';
        $this->customer_id = '';
        $this->number_table = '';
        $this->activity = '';
        $this->refreshCountBillHold();
    }
    public function refreshCountBillHold()
    {
        $this->countBillHold = Sales::where('status', 'pending')->count();
        return $this->countBillHold;
    }
    public function loadHoldBillList()
    {

        $this->holdBillList = Sales::where('status', 'pending')
            ->with('detailSales', 'customer')
            ->get();
        $this->dispatch('open-modal', id: 'ModalListBill');
    }

    public function resumeBill($slug)
    {

        $bill = Sales::with('detailSales.product', 'customer')
            ->where('slug', $slug)
            ->firstOrFail();


        $this->bill_slug = $slug;


        // Kosongkan dulu order_items biar nggak numpuk
        $this->order_items = [];
        session()->forget('orderItems'); //session di forget dulu biar nggak numpuk
        $this->order_type = $bill->order_type;
        $this->customer_id = $bill->customer_id;
        $this->number_table = $bill->table_no;
        $this->activity = $bill->activity;

        // Loop setiap item di detailSales dan masukkan ke order_items
        foreach ($bill->detailSales as $item) {
            $product = $item->product; // pastikan relasi 'product' sudah di-load

            $this->order_items[] = [
                'product_id'       => $product->id,
                'name'             => $product->name,
                'unit_price'       => $item->unit_price,
                'price'            => $item->original_price,
                'discount'         => $item->discount ?? 0,
                'discount_amount'  => $item->discount_amount ?? 0,
                'final_price'      => $item->total_price,
                'quantity'         => $item->quantity,
            ];
        }

        session()->put('orderItems', $this->order_items);
        $this->calculateSubtotal();
        $this->calculateTotal();
        $this->dispatch('close-modal', id: 'ModalListBill');
    }

    public function deleteBill($slug)
    {
        $delete = Sales::where('slug', $slug)->delete();

        $this->dispatch('close-modal', id: 'ModalListBill');
        if ($delete) {
            Notification::make('delete')
                ->title('Deletes success')
                ->success()
                ->send();
        }
        $this->countBillHold--;
    }

    //print
    function printOrderToLan($saleId)
    {
        $sale = Sales::with('detailSales', 'customer')->findOrFail($saleId);
        $printers = PrinterUser::with('printer')
        ->where('user_id', Auth::id())
        ->first();
        $ip_printer=$printers->printer->ip_address;
        try {
            $ip =$ip_printer; // IP printer
            $port = 9100; // Default thermal printer LAN port

            $connector = new NetworkPrintConnector($ip, $port);
            $printer = new Printer($connector);

            // ===== STORE HEADER =====
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Komaneka at Bisma\n");
            $printer->text("Jl. Bisma, Ubud, Kecamatan Ubud\n");
            $printer->text("Tel: (0361) 971933\n");
            $printer->text(str_repeat("=", 32) . "\n");

            // ===== ORDER INFO (With Margin) =====
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("  Invoice : {$sale->invoice_number}\n"); // 2 spaces left margin
            $printer->text("  Date    : " . now()->format('d-m-Y H:i') . "\n");

            // Customer and Activity in left-right columns
            $customerText = "Customer";
            $customerValue = $sale->customer->name ?? "Guest";
            $customerLine = "  " . $customerText . str_pad($customerValue, 32 - 2 - strlen($customerText), " ", STR_PAD_LEFT) . "\n";
            $printer->text($customerLine);

            $activityText = "Activity";
            $activityValue = $sale->activity ?? "Retail";
            $activityLine = "  " . $activityText . str_pad($activityValue, 32 - 2 - strlen($activityText), " ", STR_PAD_LEFT) . "\n";
            $printer->text($activityLine);

            $printer->text(str_repeat("-", 32) . "\n");

            // ===== ITEM LIST (With Margin) =====
            $totalItems = 0;
            foreach ($sale->detailSales as $item) {
                $totalItems += $item->quantity;

                // Product name
                $printer->text("  " . $item->product_name . "\n"); // Left margin 2 spaces

                // Format qty x unit price ........ item subtotal
                $itemSubtotal = $item->quantity * $item->unit_price;
                $line = "  " . $item->quantity . " x " . number_format($item->unit_price);
                $printer->text(
                    $line .
                        str_pad(
                            number_format($itemSubtotal),
                            32 - strlen($line), // max width 32 characters
                            " ",
                            STR_PAD_LEFT
                        ) . "\n"
                );

                // Show item discount if any
                if ($item->discount_amount > 0) {
                    $discountLine = "    Item Discount";
                    $printer->text(
                        $discountLine .
                            str_pad(
                                "-" . number_format($item->discount_amount),
                                32 - strlen($discountLine),
                                " ",
                                STR_PAD_LEFT
                            ) . "\n"
                    );

                    // Total after item discount
                    $finalItemPrice = $itemSubtotal - $item->discount_amount;
                    $totalLine = "    Item Total";
                    $printer->text(
                        $totalLine .
                            str_pad(
                                number_format($finalItemPrice),
                                32 - strlen($totalLine),
                                " ",
                                STR_PAD_LEFT
                            ) . "\n"
                    );
                }

                $printer->text("\n"); // Space between items
            }

            // ===== PRICE BREAKDOWN =====
            $printer->text(str_repeat("-", 32) . "\n");

            // Total Items
            $printer->text("  Total Items" . str_pad($totalItems, 21, " ", STR_PAD_LEFT) . "\n");

            // Subtotal (before discount and tax)
            $printer->text("  Subtotal" . str_pad(number_format($sale->subtotal), 23, " ", STR_PAD_LEFT) . "\n");

            // Total order discount if any
            if ($sale->discount_amount > 0) {
                // Check if discount is percentage or nominal
                $discountText = "  Discount";
                if ($sale->discount_percentage > 0) {
                    $discountText = "  Discount ({$sale->discount_percentage}%)";
                }

                $printer->text($discountText . str_pad("-" . number_format($sale->discount_amount), 32 - strlen($discountText), " ", STR_PAD_LEFT) . "\n");

                // Subtotal after discount
                $afterDiscount = $sale->subtotal - $sale->discount_amount;
                $printer->text("  After Discount" . str_pad(number_format($afterDiscount), 18, " ", STR_PAD_LEFT) . "\n");
            }

            // Tax (always show percentage)
            if ($sale->tax_amount > 0) {
                $taxPercentage = $sale->tax_percentage > 0 ? $sale->tax_percentage : 11; // Default 11% if not set
                $taxText = "  Tax ({$taxPercentage}%)";
                $printer->text($taxText . str_pad(number_format($sale->tax_amount), 32 - strlen($taxText), " ", STR_PAD_LEFT) . "\n");
            }

            $printer->text(str_repeat("-", 32) . "\n");

            // ===== TOTAL =====
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("  TOTAL" . str_pad(number_format($sale->total_amount), 26, " ", STR_PAD_LEFT) . "\n");
            $printer->text(str_repeat("=", 32) . "\n");

            // ===== PAYMENT INFORMATION =====
            if (isset($sale->payment_method)) {
                $printer->text("  Payment    : {$sale->payment_method}\n");
            }





            // ===== FOOTER =====
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Thank you!\n");
            $printer->text("Have a wonderful day!\n");
            $printer->feed(1);

            // ===== SIGNATURE (With Center Margin) =====
            $printer->feed(2);
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text(str_pad("Received by", 32, " ", STR_PAD_BOTH) . "\n");
            $printer->feed(3);
            $printer->text(str_pad("(__________________________)", 32, " ", STR_PAD_BOTH) . "\n");

            $printer->feed(3);
            $printer->cut();
            $printer->close();
        } catch (\Exception $e) {
            Log::error("Print failed: " . $e->getMessage());
            throw new \Exception("Failed to print receipt: " . $e->getMessage());
        }
    }


    public function render()
    {
        $products = Product::query()
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->activeCategory > 0, function ($q) {
                $q->where('category_id', $this->activeCategory);
            })
            ->with('category') // Eager load kategori untuk optimasi
            ->paginate($this->perPage === 'all' ? Product::count() : $this->perPage);

        $categories = Category::select('id', 'name')
            ->orderBy('sort_order', 'asc')
            ->get();

        $categories->prepend((object)['id' => 0, 'name' => 'All']); // Tambahkan di awal

        return view('livewire.pos', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
