<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DateTimePicker;

class SalesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Info')
                    ->description('Basic information about the sale.')
                    ->schema([
                        TextInput::make('invoice_number')
                            ->label('Invoice Number')
                            ->placeholder('Auto generated or enter manually')
                            ->default(null),

                        Select::make('customer_id')
                            ->label('Customer')
                            ->searchable()
                            ->options(fn() => \App\Models\Customer::pluck('name', 'id'))
                            ->live(onBlur: true)
                            ->placeholder('Select a customer')
                            ->default(null),

                        DateTimePicker::make('sale_date')
                            ->label('Sale Date')
                            ->required(),

                        TextInput::make('table_no')
                            ->label('Table No.')
                            ->placeholder('Enter table number (if dine-in)')
                            ->default(null),

                        Select::make('sales_type')
                            ->label('Sales Type')
                            ->options([
                                'regular' => 'Regular',
                                'complimentary' => 'Complimentary',
                                'owner_guest' => 'Owner Guest',
                                'staff_meal' => 'Staff Meal',
                                'vip_guest' => 'VIP Guest',
                                'business_entertainment' => 'Business Entertainment',
                                'banquet/wedding' => 'Banquet / Wedding',
                            ])
                            ->default('regular')
                            ->required(),

                        Select::make('order_type')
                            ->label('Order Type')
                            ->options([
                                'dine_in' => 'Dine In',
                                'room_service' => 'Room Service',
                                'takeaway' => 'Takeaway',
                                'other' => 'Other',
                            ])
                            ->default('dine_in')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Amounts & Payment')
                    ->description('Financial details of the transaction.')
                    ->schema([
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->prefix('Rp')
                            ->required()
                            ->numeric(),

                        TextInput::make('tax_amount')
                            ->label('Tax Amount')
                            ->prefix('Rp')
                            ->required()
                            ->numeric()
                            ->default(0.0),

                        TextInput::make('discount_amount')
                            ->label('Discount')
                            ->prefix('Rp')
                            ->required()
                            ->numeric()
                            ->default(0.0),

                        TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->prefix('Rp')
                            ->required()
                            ->numeric(),

                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'cash' => 'Cash',
                                'card' => 'Card',
                                'qris' => 'QRIS',
                                'room_charge' => 'Room Charge',
                                'complimentary' => 'Complimentary',
                            ])
                            ->default('room_charge')
                            ->required(),

                        TextInput::make('total_items')
                            ->label('Total Items')
                            ->required()
                           
                            ->numeric(),

                        Textarea::make('payment_details')
                            ->label('Payment Details')
                            ->placeholder('Enter transaction details if needed')
                            ->default(null)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Activity & Status')
                    ->description('Tracking sale activity and progress.')
                    ->schema([
                        Select::make('activity')
                            ->label('Activity')
                            ->options([
                                'breakfast' => 'Breakfast',
                                'lunch' => 'Lunch',
                                'dinner' => 'Dinner',
                                'afternoontea' => 'Afternoon Tea',
                            ])
                            ->required(),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'completed' => 'Completed',
                                'pending' => 'Pending',
                                'cancelled' => 'Cancelled',
                                'refunded' => 'Refunded',
                            ])
                            ->default('pending')
                            ->required(),

                        TextInput::make('user_id')
                            ->label('User ID')
                            ->numeric()
                            ->required()
                            ->default(null),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->placeholder('Enter additional notes')
                            ->default(null)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Sales Details')
                    ->description('List of products sold in this transaction.')
                    ->schema([
                        Repeater::make('detailSales')
                            ->label('Sales Items')
                            ->live()
                            ->afterStateUpdated(
                                function ($state, callable $set, callable $get) {
                                    // Hitung subtotal dari semua total_price di detailSales
                                    $subtotal = collect($state)->sum('total_price');
                                    $set('subtotal', $subtotal);

                                    $itemCount = is_array($state) ? count($state) : 0;
        
        // Mengisi field 'total_items' dengan jumlah item
        $set('total_items', $itemCount);

                                   
                                }
                            )
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->columnSpanFull()
                                    ->searchable()
                                    ->options(fn() => \App\Models\Product::pluck('name', 'id'))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(
                                        function ($state, callable $set, callable $get) {
                                            // Dapatkan harga produk yang dipilih
                                            $unitPrice = \App\Models\Product::find($state)?->price ?? 0;
                                            // Set unit_price
                                            $set('unit_price', $unitPrice);
                                            // Hitung total_price berdasarkan unit_price dan quantity yang ada
                                            $set('total_price', $unitPrice * $get('quantity'));
                                        }
                                    )
                                    ->required(),

                                TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->live()
                                    ->default(1)
                                    ->afterStateUpdated(
                                        fn($state, callable $set, $get) =>
                                        $set('total_price', $state * $get('unit_price'))
                                    )
                                    ->required(),

                                TextInput::make('unit_price')
                                    ->label('Unit Price')
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->numeric()
                                    ->required(),

                                TextInput::make('total_price')
                                    ->label('Total Price')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->readOnly()
                                    ->required(),
                            ])
                            ->columns(3),
                    ]),

                Hidden::make('slug')
                    ->required(),
            ]);
    }
}
