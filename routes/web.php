<?php

use App\Livewire\PropertyListing;
use App\Models\Property;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('properties.index');
});


Route::get('/properties', PropertyListing::class)->name('properties.index');

//show
Route::get('/properties/{property:slug}', function (Property $property) {
    //only show the available properties
    if (!$property->isAvailable()) {
        abort(404);
    }
    return view('properties.show', compact('property'));
})->name('property.show');
