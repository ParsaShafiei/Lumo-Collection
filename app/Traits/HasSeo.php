<?php
namespace App\Traits;

use App\Models\SeoMeta;
use App\Models\Sitemap;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeo
{
    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    public function sitemap(): MorphOne
    {
        return $this->morphOne(Sitemap::class, 'sitemapable');
    }

    // Falls back gracefully: seo title → model name → app name
    public function getSeoTitleAttribute(): string
    {
        return $this->seo?->meta_title
            ?? $this->name
            ?? config('app.name');
    }

    public function getSeoDescriptionAttribute(): string
    {
        return $this->seo?->meta_description
            ?? $this->short_description
            ?? $this->description
            ?? '';
    }
}
