<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        // 10-column root grid:
        //   left  → span 7  (~70%)
        //   right → span 3  (~30%)
        // Sidebar sections live inside one Group so they expand
        // independently — no gap bleed into the left column.
        return $schema
            ->columns(10)
            ->components([

                // ══════════════════════════════════════════════
                // LEFT  — span 7
                // ══════════════════════════════════════════════
                Group::make()
                    ->columnSpan(7)
                    ->schema([

                        Section::make('Product Information')
                            ->icon('heroicon-o-cube')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(
                                            fn($state, callable $set) =>
                                            $set('slug', \Str::slug($state))
                                        ),

                                    TextInput::make('slug')
                                        ->required()
                                        ->unique(Product::class, 'slug', ignoreRecord: true)
                                        ->maxLength(255)
                                        ->prefix('/')
                                ]),

                                Grid::make(2)->schema([
                                    Select::make('category_id')
                                        ->label('Category')
                                        ->relationship('category', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->createOptionForm([
                                            TextInput::make('name')->required(),
                                            TextInput::make('slug')
                                        ]),

                                    Select::make('brand_id')
                                        ->label('Brand')
                                        ->relationship('brand', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->createOptionForm([
                                            TextInput::make('name')->required(),
                                            TextInput::make('slug')
                                        ]),
                                ]),

                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->required()
                                    ->unique(Product::class, 'sku', ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('Must be unique across all products.'),

                                Textarea::make('short_description')
                                    ->label('Short Description')
                                    ->rows(3)
                                    ->maxLength(300)
                                    ->helperText('Used in product cards and meta fallback.')
                                    ->columnSpanFull(),
                                RichEditor::make('description')
                                    ->label('Full Description')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'bulletList',
                                        'orderedList',
                                        'link',
                                        'blockquote',
                                        'undo',
                                        'redo',
                                    ])
                                    ->columnSpanFull(),

                            ]),

                        Section::make('Pricing')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('base_price')
                                        ->label('Base Price')
                                        ->numeric()
                                        ->prefix('﷼')
                                        ->required()
                                        ->minValue(0),

                                    TextInput::make('sale_price')
                                        ->label('Sale Price')
                                        ->numeric()
                                        ->prefix('﷼')
                                        ->nullable()
                                        ->minValue(0)
                                        ->helperText('Leave empty if no active sale.'),
                                ]),
                            ]),

                        Section::make('Product Variants')
                            ->icon('heroicon-o-swatch')
                            ->description('Add variants for different colors, lens types, frame materials and sizes.')
                            ->collapsible()
                            ->schema([
                                Repeater::make('variants')
                                    ->relationship('variants')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextInput::make('sku')
                                                ->label('Variant SKU')
                                                ->required()
                                                ->unique('product_variants', 'sku', ignoreRecord: true)
                                                ->maxLength(255),

                                            TextInput::make('stock_qty')
                                                ->label('Stock')
                                                ->numeric()
                                                ->default(0)
                                                ->minValue(0)
                                                ->required()
                                                ->suffix('units'),

                                            TextInput::make('price_modifier')
                                                ->label('Price Modifier')
                                                ->numeric()
                                                ->prefix('﷼')
                                                ->default(0)
                                                ->helperText('+ or − from base price.'),
                                        ]),

                                        Grid::make(3)->schema([
                                            Select::make('color')
                                                ->label('Frame Color')
                                                ->options([
                                                    'black' => 'Black',
                                                    'brown' => 'Brown',
                                                    'gold' => 'Gold',
                                                    'silver' => 'Silver',
                                                    'tortoise' => 'Tortoise',
                                                    'transparent' => 'Transparent',
                                                    'blue' => 'Blue',
                                                    'red' => 'Red',
                                                ])
                                                ->searchable()
                                                ->nullable(),

                                            Select::make('frame_material')
                                                ->label('Frame Material')
                                                ->options([
                                                    'acetate' => 'Acetate',
                                                    'titanium' => 'Titanium',
                                                    'tr90' => 'TR90',
                                                    'stainless' => 'Stainless Steel',
                                                    'aluminum' => 'Aluminum',
                                                    'wood' => 'Wood',
                                                    'carbon' => 'Carbon Fiber',
                                                ])
                                                ->searchable()
                                                ->nullable(),

                                            Select::make('size')
                                                ->options([
                                                    'xs' => 'XS',
                                                    'small' => 'Small',
                                                    'medium' => 'Medium',
                                                    'large' => 'Large',
                                                    'xl' => 'XL',
                                                ])
                                                ->nullable(),
                                        ]),

                                        Grid::make(2)->schema([
                                            Select::make('lens_type')
                                                ->label('Lens Type')
                                                ->options([
                                                    'standard' => 'Standard',
                                                    'polarized' => 'Polarized',
                                                    'mirrored' => 'Mirrored',
                                                    'photochromic' => 'Photochromic',
                                                    'gradient' => 'Gradient',
                                                    'uv400' => 'UV400',
                                                ])
                                                ->searchable()
                                                ->nullable(),

                                            Select::make('lens_color')
                                                ->label('Lens Color')
                                                ->options([
                                                    'clear' => 'Clear',
                                                    'black' => 'Black',
                                                    'brown' => 'Brown',
                                                    'grey' => 'Grey',
                                                    'green' => 'Green',
                                                    'blue' => 'Blue',
                                                    'yellow' => 'Yellow',
                                                    'pink' => 'Pink',
                                                    'orange' => 'Orange',
                                                    'red' => 'Red',
                                                    'silver' => 'Silver Mirror',
                                                    'gold' => 'Gold Mirror',
                                                ])
                                                ->searchable()
                                                ->nullable(),
                                        ]),
                                    ])
                                    ->itemLabel(
                                        fn(array $state): ?string =>
                                        collect([
                                            $state['color'] ?? null,
                                            $state['frame_material'] ?? null,
                                            $state['lens_type'] ?? null,
                                            $state['size'] ?? null,
                                        ])
                                            ->filter()
                                            ->map(fn($v) => ucfirst($v))
                                            ->join(' · ')
                                        ?: 'New Variant'
                                    )
                                    ->addActionLabel('+ Add Variant')
                                    ->collapsible()
                                    ->cloneable()
                                    ->reorderable()
                                    ->defaultItems(0)
                                    ->columns(1),
                            ]),

                        Section::make('Product Images')
                            ->icon('heroicon-o-photo')
                            ->collapsible()
                            ->schema([
                                Repeater::make('images')
                                    ->relationship('images')
                                    ->schema([
                                        FileUpload::make('path')
                                            ->label('Image')
                                            ->image()
                                            ->imageEditor()
                                            ->directory('products')
                                            ->required()
                                            ->columnSpanFull(),

                                        Grid::make(2)->schema([
                                            TextInput::make('alt_text')
                                                ->label('Alt Text')
                                                ->helperText('Describe the image for SEO & accessibility.'),

                                            TextInput::make('sort_order')
                                                ->label('Sort Order')
                                                ->numeric()
                                                ->default(0),
                                        ]),

                                        Toggle::make('is_primary')
                                            ->label('Set as Primary Image')
                                            ->default(false),
                                    ])
                                    ->addActionLabel('+ Add Image')
                                    ->defaultItems(0)
                                    ->collapsible()
                                    ->reorderable()
                                    ->columns(1),
                            ]),

                    ]),

                // ══════════════════════════════════════════════
                // RIGHT SIDEBAR — span 3
                // All sidebar sections are inside one Group so
                // they stack and expand without affecting the
                // left column row heights at all.
                // ══════════════════════════════════════════════
                Group::make()
                    ->columnSpan(3)
                    ->schema([

                        Section::make('Status')
                            ->icon('heroicon-o-signal')
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->helperText('Visible in the store.')
                                    ->default(true),

                                Toggle::make('is_featured')
                                    ->label('Featured')
                                    ->helperText('Show on homepage / featured section.')
                                    ->default(false),

                                Toggle::make('is_indexable')
                                    ->label('SEO Indexable')
                                    ->helperText('Allow search engines to index.')
                                    ->default(true),
                            ]),

                        Section::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                TextInput::make('seo.meta_title')
                                    ->label('Meta Title')
                                    ->maxLength(160)
                                    ->helperText('50–60 chars recommended.'),

                                Textarea::make('seo.meta_description')
                                    ->label('Meta Description')
                                    ->rows(3)
                                    ->maxLength(320)
                                    ->helperText('150–160 chars recommended.'),

                                TextInput::make('seo.meta_keywords')
                                    ->label('Keywords'),

                                TextInput::make('seo.canonical_url')
                                    ->label('Canonical URL')
                                    ->url(),

                                Select::make('seo.robots')
                                    ->label('Robots')
                                    ->options([
                                        'index,follow' => 'Index, Follow',
                                        'noindex,follow' => 'No Index, Follow',
                                        'index,nofollow' => 'Index, No Follow',
                                        'noindex,nofollow' => 'No Index, No Follow',
                                    ])
                                    ->default('index,follow'),
                            ]),

                    ]),

            ]);
    }
}
