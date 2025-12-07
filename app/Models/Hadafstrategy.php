<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class Hadafstrategy extends Model
{
    use HasFactory, BelongsToOrganization;
    protected $fillable = ['id', 'percentage', 'name', 'parent_id','user_id'];

}
