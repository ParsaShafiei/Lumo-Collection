<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = ['from_url', 'to_url', 'type', 'is_active', 'hit_count'];

    public function incrementHit(): void
    {
        $this->increment('hit_count');
    }
}
