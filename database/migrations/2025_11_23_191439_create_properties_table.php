<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            //basic information
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['apartment', 'house', 'condo', 'land','townhouse','villa','commercial']);
            $table->enum('listing_type', ['sale', 'rent'])->default('sale');
            $table->enum('status', ['available', 'sold', 'pending','draft','rented'])->default('available');
            // Pricing
            $table->decimal('price', 12, 2);
            $table->decimal('price_per_sqft', 8, 2)->nullable();
            // Location
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('country')->default('Jordan');
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            // Property Details
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('total_area')->nullable(); // in square feet
            $table->integer('built_year')->nullable();
            $table->boolean('furnished')->default(false);
            $table->boolean('parking')->default(false);
            $table->integer('parking_spaces')->nullable();

             // Features (JSON for flexibility)
            $table->json('features')->nullable(); // pool, garden, security, etc.
            $table->json('images')->nullable(); // array of image paths

             // SEO
            $table->string('slug')->unique();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // Visibility
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('featured_until')->nullable();

             // Contact Information
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['type', 'listing_type', 'status']);
            $table->index(['city', 'state']);
            $table->index(['price', 'listing_type']);
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
