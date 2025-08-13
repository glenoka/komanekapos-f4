<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                ->label('Product Name')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (string $context, $state, callable $set) => 
                    $context === 'edit' ? null : $set('slug', Str::slug($state))
                ),

            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->rules(['alpha_dash'])
                ->helperText('URL-friendly version of the name'),

            Select::make('category_id')
                ->label('Category')
                ->options(Category::pluck('name','id'))
                ->required()
                ->searchable()
                ->preload()
                ->createOptionForm([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                ]
            )
            ->createOptionUsing(function (array $data) {
                $category = Category::create($data);
                return $category->id;
            }),

            TextInput::make('price')
                ->label('Price')
                ->required()
                ->numeric()
                ->prefix('IDR')
                ->step(0.01)
                ->minValue(0)
                ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.')),

            FileUpload::make('path_images')
                ->label('Product Images')
                ->image()
                ->directory('products')
                ->imageEditor()
                ->imageEditorAspectRatios([
                    '16:9',
                    '4:3',
                    '1:1',
                ])
                ->maxFiles(5)
                ->reorderable(),

            Select::make('status')
                ->label('Status')
                ->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'draft' => 'Draft',
                    'out_of_stock' => 'Out of Stock',
                ])
                ->default('active')
                ->required(),
            ]);
    }
}
