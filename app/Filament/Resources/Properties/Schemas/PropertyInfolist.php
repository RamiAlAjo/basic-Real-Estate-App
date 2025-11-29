<?php

namespace App\Filament\Resources\Properties\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PropertyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('description')
                    ->columnSpanFull(),
                TextEntry::make('type')
                    ->badge(),
                TextEntry::make('listing_type')
                    ->badge(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('price')
                    ->money(),
                TextEntry::make('price_per_sqft')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('address'),
                TextEntry::make('city'),
                TextEntry::make('state'),
                TextEntry::make('country'),
                TextEntry::make('postal_code')
                    ->placeholder('-'),
                TextEntry::make('latitude')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('longitude')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('bedrooms')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('bathrooms')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('total_area')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('built_year')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('furnished')
                    ->boolean(),
                IconEntry::make('parking')
                    ->boolean(),
                TextEntry::make('parking_spaces')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('features')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('images')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('slug'),
                TextEntry::make('meta_title')
                    ->placeholder('-'),
                TextEntry::make('meta_description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_featured')
                    ->boolean(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('featured_until')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('contact_name')
                    ->placeholder('-'),
                TextEntry::make('contact_phone')
                    ->placeholder('-'),
                TextEntry::make('contact_email')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
