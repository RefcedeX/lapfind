<?php

namespace App\Models;

use Database\Factories\AlternativeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'brand', 'price', 'image', 'description'])]
class Alternative extends Model
{
    /** @use HasFactory<AlternativeFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    /**
     * Get the Indonesian Rupiah formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.number_format((float) $this->price, 0, ',', '.');
    }

    /**
     * Get the display image URL (stored image, local asset, or intelligent fallback).
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            if (file_exists(public_path($this->image))) {
                return asset($this->image);
            }
            if (file_exists(public_path('storage/'.$this->image))) {
                return asset('storage/'.$this->image);
            }
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                return $this->image;
            }
        }

        $lowerName = strtolower($this->name.' '.$this->brand);
        $map = [
            'macbook' => 'images/laptops/macbook-air-m2.jpg',
            'zenbook' => 'images/laptops/asus-zenbook-14.jpg',
            'vivobook' => 'images/laptops/asus-vivobook-14.jpg',
            'yoga' => 'images/laptops/lenovo-yoga-slim-7.jpg',
            'ideapad' => 'images/laptops/lenovo-ideapad-slim-3.jpg',
            'swift' => 'images/laptops/acer-swift-go-14.jpg',
            'aspire' => 'images/laptops/acer-aspire-3.jpg',
            'modern' => 'images/laptops/msi-modern-14.jpg',
            'msi' => 'images/laptops/msi-modern-14.jpg',
            'inspiron' => 'images/laptops/dell-inspiron-14.jpg',
            'dell' => 'images/laptops/dell-inspiron-14.jpg',
            '14s' => 'images/laptops/hp-14s.jpg',
            'hp' => 'images/laptops/hp-14s.jpg',
            'hype' => 'images/laptops/axioo-hype-5.jpg',
            'axioo' => 'images/laptops/axioo-hype-5.jpg',
            'inbook' => 'images/laptops/infinix-inbook-y2.jpg',
            'infinix' => 'images/laptops/infinix-inbook-y2.jpg',
            'tuf' => 'images/laptops/asus-vivobook-14.jpg',
            'rog' => 'images/laptops/asus-zenbook-14.jpg',
            'zephyrus' => 'images/laptops/asus-zenbook-14.jpg',
            'strix' => 'images/laptops/asus-zenbook-14.jpg',
            'legion' => 'images/laptops/dell-inspiron-14.jpg',
            'loq' => 'images/laptops/lenovo-ideapad-slim-3.jpg',
            'nitro' => 'images/laptops/acer-aspire-3.jpg',
            'predator' => 'images/laptops/acer-swift-go-14.jpg',
            'cyborg' => 'images/laptops/msi-modern-14.jpg',
            'katana' => 'images/laptops/msi-modern-14.jpg',
            'victus' => 'images/laptops/hp-14s.jpg',
            'omen' => 'images/laptops/hp-14s.jpg',
            'pongo' => 'images/laptops/axioo-hype-5.jpg',
        ];

        foreach ($map as $key => $path) {
            if (str_contains($lowerName, $key) && file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return asset('images/laptops/asus-zenbook-14.jpg');
    }

    /**
     * Get all criteria values for this alternative.
     *
     * @return HasMany<AlternativeValue, $this>
     */
    public function values(): HasMany
    {
        return $this->hasMany(AlternativeValue::class, 'alternative_id');
    }

    /**
     * Get all criteria associated with this alternative.
     *
     * @return BelongsToMany<Criterion, $this>
     */
    public function criteria(): BelongsToMany
    {
        return $this->belongsToMany(Criterion::class, 'alternative_values', 'alternative_id', 'criteria_id')
            ->withPivot('value')
            ->withTimestamps();
    }
}
