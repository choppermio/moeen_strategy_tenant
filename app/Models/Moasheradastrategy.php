<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class Moasheradastrategy extends Model
{
    use HasFactory, BelongsToOrganization;
    protected $fillable = ['id', 'percentage', 'name', 'parent_id','user_id'];

    public function todos()
    {
      return $this->belongsToMany(Todo::class);
    }
    
    public function mobadaras()
    {
      return $this->belongsToMany(Mubadara::class);
    }
    
    public function moashermkmfs()
    {
      return $this->belongsToMany(Moashermkmf::class, 'moasheradastrategy_moashermkmf');
    }
}
