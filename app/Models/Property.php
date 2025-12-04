<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'listing_type',
        'status',
        'price',
        'price_per_sqft',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'bedrooms',
        'bathrooms',
        'total_area',
        'built_year',
        'furnished',
        'parking',
        'parking_spaces',
        'features',
        'images',
        'slug',
        'meta_title',
        'meta_description',
        'is_featured',
        'is_active',
        'featured_until',
        'contact_name',
        'contact_phone',
        'contact_email',
    ];

    protected $casts = [
        'features'        => 'array',
        'images'          => 'array',
        'furnished'       => 'boolean',
        'parking'         => 'boolean',
        'is_featured'     => 'boolean',
        'is_active'       => 'boolean',
        'featured_until'  => 'datetime',
        'price'           => 'decimal:2',
        'price_per_sqft'  => 'decimal:2',
        'latitude'        => 'decimal:8',
        'longitude'       => 'decimal:8',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($property) {
            if (empty($property->slug)) {
                $property->slug = Str::slug($property->title);
            }
        });

        static::updating(function ($property) {
            if ($property->isDirty('title')) {
                $property->slug = Str::slug($property->title);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    #[Scope]
    public function available(Builder $query)
    {
        return $query->where('status', 'available')
                     ->where('is_active', true);
    }

    #[Scope]
    public function forSale(Builder $query)
    {
        return $query->where('listing_type', 'sale');
    }

    #[Scope]
    public function forRent(Builder $query)
    {
        return $query->where('listing_type', 'rent');
    }

    #[Scope]
    public function featured(Builder $query)
    {
        return $query->where('is_featured', true)
                     ->where(function ($q) {
                         $q->whereNull('featured_until')
                           ->orWhere('featured_until', '>', now());
                     });
    }

    #[Scope]
    public function inCity(Builder $query, string $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    #[Scope]
    public function priceBetween(Builder $query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    #[Scope]
    public function byType(Builder $query, string $type)
    {
        return $query->where('type', $type);
    }

    #[Scope]
    public function withBedrooms(Builder $query, int $bedrooms)
    {
        return $query->where('bedrooms', '>=', $bedrooms);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getFormattedPriceAttribute(): string
    {
        return 'JOD ' . number_format($this->price ?? 0, 0);
    }

    public function getFullAddressAttribute(): string
    {
        return trim("{$this->address}, {$this->city}, {$this->state}, {$this->country}", ', ');
    }

    public function getMainImageAttribute(): ?string
    {
        return $this->images[0] ?? null;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->main_image ? Storage::url($this->main_image) : null;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'available' => 'success',
            'sold'      => 'danger',
            'rented'    => 'warning',
            'pending'   => 'info',
            'draft'     => 'secondary',
            default     => 'secondary',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'house'      => '🏠',
            'apartment'  => '🏢',
            'condo'      => '🏬',
            'townhouse'  => '🏘️',
            'villa'      => '🏡',
            'land'       => '🌍',
            'commercial' => '🏢',
            default      => '🏠',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */
    public function isFeatured(): bool
    {
        if (!$this->is_featured) {
            return false;
        }

        return !$this->featured_until || $this->featured_until->isFuture();
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->is_active;
    }

    public function calculatePricePerSqft(): void
    {
        if ($this->total_area > 0) {
            $this->price_per_sqft = $this->price / $this->total_area;
            $this->save();
        }
    }

    public function addFeature(string $feature): void
    {
        $features = $this->features ?? [];

        if (!in_array($feature, $features)) {
            $features[] = $feature;
            $this->features = $features;
            $this->save();
        }
    }

    public function removeFeature(string $feature): void
    {
        $features = $this->features ?? [];

        $this->features = array_values(array_diff($features, [$feature]));
        $this->save();
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /*
    |--------------------------------------------------------------------------
    | Static Helpers
    |--------------------------------------------------------------------------
    */
    public static function getPropertyTypes(): array
    {
        return [
            'house'      => 'House',
            'apartment'  => 'Apartment',
            'condo'      => 'Condo',
            'townhouse'  => 'Townhouse',
            'villa'      => 'Villa',
            'land'       => 'Land',
            'commercial' => 'Commercial',
        ];
    }

    public static function getListingTypes(): array
    {
        return [
            'sale' => 'For Sale',
            'rent' => 'For Rent',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            'draft'     => 'Draft',
            'available' => 'Available',
            'pending'   => 'Pending',
            'sold'      => 'Sold',
            'rented'    => 'Rented',
        ];
    }
}
