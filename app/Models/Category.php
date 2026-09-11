<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['slug', 'layout_type', 'accent_color', 'icon'];

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

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->nameIn($locale) ?? $this->nameIn('ne') ?? $this->nameIn('en') ?? $this->slug;
    }
}