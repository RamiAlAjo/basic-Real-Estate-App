<?php

namespace App\Filament\Resources\Properties\Schemas;

use App\Models\Property;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Get;

class PropertyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columnSpanFull()
                    ->description('Provide the basic details of the property.')
                    ->columns(2)
                    ->schema([
                        Grid::make(2)
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('title')
                                    ->required(),
                                TextInput::make('slug')
                                    ->readOnly(),
                            ]),
                        Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Grid::make(3)
                            ->columnSpanFull()
                            ->schema([
                                Select::make('type')
                                    ->options(Property::getPropertyTypes())
                                    ->required(),
                                Select::make('listing_type')
                                    ->options(Property::getListingTypes())
                                    ->default('sale')
                                    ->required(),
                                Select::make('status')
                                    ->options(Property::getStatuses())
                                    ->default('available')
                                    ->required(),
                            ])
                    ]),

                Section::make('Pricing')
                    ->columnSpanFull()
                    ->description('Set the pricing details for the property.')
                    ->schema([
                        Grid::make(2)
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('JOD'),
                                TextInput::make('price_per_sqft')
                                    ->numeric()
                                    ->prefix('JOD')
                                    ->default(null),
                            ]),
                    ]),

                Section::make('Location')
                    ->columnSpanFull()
                    ->description('Provide the location details of the property.')
                    ->schema([
                        Textarea::make('address')
                            ->columnSpanFull()
                            ->required(),
                        Grid::make(3)
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('country')
                                    ->required()
                                    ->default('Jordan'),
                                TextInput::make('state')
                                    ->required()
                                    ->default('Amman'),
                                TextInput::make('city')
                                    ->required()
                                    ->default('Amman'),
                                TextInput::make('postal_code')
                                    ->default(null),
                            ]),
                        Grid::make(2)
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('latitude')
                                    ->numeric()
                                    ->default(null),
                                TextInput::make('longitude')
                                    ->numeric()
                                    ->default(null),
                            ])
                    ]),

                Section::make('Details')
                    ->columnSpanFull()
                    ->description('Additional details about the property.')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('bedrooms')
                                    ->numeric()
                                    ->default(null),
                                TextInput::make('bathrooms')
                                    ->numeric()
                                    ->default(null),
                                TextInput::make('total_area')
                                    ->numeric()
                                    ->prefix('sqft')
                                    ->default(null),
                                TextInput::make('built_year')
                                    ->numeric()
                                    ->default(null),
                            ]),
                        Grid::make(2)
                            ->columnSpanFull()
                            ->schema([
                                Toggle::make('furnished')
                                    ->required(),
                                Toggle::make('parking')
                                    ->live()
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->columnSpanFull()
                            ->schema([
                                TagsInput::make('features')
                                    ->helperText('Add features like garden, pool, gym, etc.'),
                                TextInput::make('parking_spaces')
                                    ->numeric()
                                    ->visible(fn(Get $get): bool => $get('parking'))
                                    ->default(null),
                            ])
                    ]),

                Section::make('Media & SEO')
                    ->columnSpanFull()
                    ->description('Upload images and set SEO details.')
                    ->schema([
                        FileUpload::make('images')
                            ->multiple()
                            ->image()
                            ->panelLayout('grid')
                            ->maxFiles(10)
                            ->disk('public')
                            ->directory('properties-images')
                            ->columnSpanFull(),
                        Grid::make(2)
                            ->columnSpanFull()
                            ->schema([

                                TextInput::make('meta_title')
                                    ->default(null),
                                Textarea::make('meta_description')
                                    ->default(null)
                                    ->columnSpanFull(),
                                Toggle::make('is_featured')
                                    ->live()
                                    ->required(),
                                Toggle::make('is_active')
                                    ->default(true)
                                    ->required(),
                                DateTimePicker::make('featured_until')
                                    ->visible(fn(Get $get): bool => $get('is_featured')),
                            ])
                    ]),

                Section::make('Contact Information')
                    ->columnSpanFull()
                    ->description('Provide contact details for inquiries.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('contact_name')
                            ->default(null),
                        TextInput::make('contact_phone')
                            ->tel()
                            ->default(null),
                        TextInput::make('contact_email')
                            ->email()
                            ->default(null),
                    ]),
            ]);
    }
}
