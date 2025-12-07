<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class Image extends Model
{
    use HasFactory, BelongsToOrganization;
    protected $fillable = ['filename', 'filepath'];

    public function imageable() {
        return $this->morphTo();
    }
}
