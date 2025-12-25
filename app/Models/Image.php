<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory, BelongsToOrganization;
    protected $fillable = ['filename', 'filepath', 'disk'];

    public function imageable() {
        return $this->morphTo();
    }

    /**
     * Get the full URL for the image
     */
    public function getUrlAttribute()
    {
        return image_url($this);
    }

    /**
     * Get the image URL (alias for getUrlAttribute)
     */
    public function url()
    {
        return $this->getUrlAttribute();
    }
}
