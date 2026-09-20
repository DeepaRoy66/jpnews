<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'slug', 'layout_type', 'accent_color', 'icon',
        'show_in_navbar', 'nav_order',
        'show_in_navbar_ne', 'show_in_navbar_en',
    ];

    protected $casts = [
        'show_in_navbar'    => 'boolean',
        'show_in_navbar_ne' => 'boolean',
        'show_in_navbar_en' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function nameIn(string $locale): ?string
    {
        $translation = $this->translations->firstWhere('locale', $locale);
        return $translation?->name;
    }

    public function scopeInNavbarFor($query, string $locale)
    {
        $column = $locale === 'ne' ? 'show_in_navbar_ne' : 'show_in_navbar_en';

        return $query->where($column, true)
                     ->orderByRaw('nav_order IS NULL, nav_order ASC')
                     ->orderBy('id');
    }

    public function subcategories()
{
    return $this->hasMany(Category::class, 'parent_id');
}

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->nameIn($locale) ?? $this->nameIn('ne') ?? $this->nameIn('en') ?? $this->slug;
    }
}