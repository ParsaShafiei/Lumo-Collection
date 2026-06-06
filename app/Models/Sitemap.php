<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Sitemap extends Model
{
    protected $fillable = [
        'url',
        'change_freq',
        'priority',
        'last_modified_at',
    ];

    protected $casts = [
        'last_modified_at' => 'datetime',
        'priority' => 'float',
    ];

    public function sitemapable(): MorphTo
    {
        return $this->morphTo();
    }
}
