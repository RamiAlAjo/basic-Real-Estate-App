<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Property;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

#[Title('Property Listings')]
class PropertyListing extends Component
{
    use WithPagination;
    public $search = '';
    public $minPrice = '';
    public $maxPrice = '';
    public $type = '';
    public $city = '';
    public $listingType = '';
    public $minBedrooms = '';
    public $featuredOnly = false;
    // sorting
    public $sortBy = 'created_at'; // default sorting option
    public $sortDirection = 'desc'; // default sorting direction
    public $viewMode = 'grid'; // default view mode

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'listingType' => ['except' => ''],
        'city' => ['except' => ''],
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
        'minBedrooms' => ['except' => ''],
        'featuredOnly' => ['except' => false],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'page' => ['except' => 1], // 2
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function updatingListingType()
    {
        $this->resetPage();
    }

    public function updatingCity()
    {
        $this->resetPage();
    }

    public function updatingMinPrice()
    {
        $this->resetPage();
    }

    public function updatingMaxPrice()
    {
        $this->resetPage();
    }

    public function updatingMinBedrooms()
    {
        $this->resetPage();
    }

    public function updatingFeaturedOnly()
    {
        $this->resetPage();
    }
    //sorting
    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    //clear filters
    public function clearFilters()
    {
        $this->reset(['search', 'minPrice', 'maxPrice', 'type', 'listingType', 'minBedrooms', 'featuredOnly', 'sortBy', 'sortDirection']);
        $this->resetPage();
    }

    //change view mode (grid or list)
    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }
    //computed properties
    #[Computed]
    public function properties()
    {
        return Property::query()
            ->available()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->type, fn($query) => $query->byType($this->type))
            ->when(
                $this->listingType,
                fn($query) =>
                $this->listingType === 'sale'
                ? $query->forSale()
                : $query->forRent()
            )
            ->when($this->city, fn($query) => $query->inCity($this->city))
            ->when($this->minPrice, fn($query) => $query->where('price', '>=', $this->minPrice))
            ->when($this->maxPrice, fn($query) => $query->where('price', '<=', $this->maxPrice))
            ->when($this->minBedrooms, fn($query) => $query->withBedrooms($this->minBedrooms))
            ->when($this->featuredOnly, fn($query) => $query->featured())
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(6);
    }

    #[Computed]
    public function propertyTypes()
    {
        return Property::getPropertyTypes();
    }
    #[Computed]
    public function listingTypes()
    {
        return Property::getListingTypes();
    }

    #[Computed]
    public function cities()
    {
        return Property::query()
            ->available()
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');
    }

    public function render()
    {
        return view('livewire.property-listing', [
            'properties' => $this->properties(),
            'propertyTypes' => $this->propertyTypes(),
            'listingTypes' => $this->listingTypes(),
            'cities' => $this->cities(),
        ]);
    }
}
