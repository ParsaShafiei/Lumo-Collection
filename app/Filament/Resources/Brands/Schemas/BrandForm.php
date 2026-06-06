<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('meta_description')
                    ->columnSpanFull(),
                Toggle::make('is_indexable')
                    ->label('Indexable')
                    ->default(false)
                    ->required(),
                RichEditor::make('description')
                    ->columnSpanFull(),
                FileUpload::make('logo_path'),
            ]);
    }
}
