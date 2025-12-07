<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class Moasher extends Model
{
    use HasFactory, BelongsToOrganization;

    public static function getUniqueKafaaIds()
    {
        // return self::groupBy('moasher_kafaa_id')->get();
        }

}
