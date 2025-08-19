<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SalesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('invoice_number')
                    ->default(null),
                TextInput::make('customer_id')
                    ->numeric()
                    ->default(null),
                DateTimePicker::make('sale_date')
                    ->required(),
                TextInput::make('table_no')
                    ->default(null),
                Select::make('sales_type')
                    ->options([
            'regular' => 'Regular',
            'complimentary' => 'Complimentary',
            'owner_guest' => 'Owner guest',
            'staff_meal' => 'Staff meal',
            'vip_guest' => 'Vip guest',
            'business_entertainment' => 'Business entertainment',
            'banquet/wedding' => 'Banquet/wedding',
        ])
                    ->default('regular'),
                Select::make('order_type')
                    ->options([
            'dine_in' => 'Dine in',
            'room_service' => 'Room service',
            'takeaway' => 'Takeaway',
            'other' => 'Other',
        ])
                    ->default('dine_in')
                    ->required(),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                TextInput::make('tax_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                Select::make('payment_method')
                    ->options([
            'cash' => 'Cash',
            'card' => 'Card',
            'qris' => 'Qris',
            'room_charge' => 'Room charge',
            'complimentary' => 'Complimentary',
        ])
                    ->default('room_charge'),
                TextInput::make('total_items')
                    ->required()
                    ->numeric(),
                Textarea::make('payment_details')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('activity')
                    ->options([
            'breakfast' => 'Breakfast',
            'lunch' => 'Lunch',
            'dinner' => 'Dinner',
            'afternoontea' => 'Afternoontea',
        ])
                    ->required(),
                Select::make('status')
                    ->options([
            'completed' => 'Completed',
            'pending' => 'Pending',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
        ])
                    ->default('pending')
                    ->required(),
                TextInput::make('user_id')
                    ->numeric()
                    ->default(null),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('slug')
                    ->required(),
            ]);
    }
}
