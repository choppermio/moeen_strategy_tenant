<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class EmployeePositionRelation extends Model
{
    use HasFactory, BelongsToOrganization;

 //fillable 
    protected $fillable = ['parent_id', 'child_id', 'percentage'];
   
    public function employeePosition() {
        return $this->belongsTo(EmployeePosition::class, );
    }
    
    public function childUser() {
        return $this->belongsTo(User::class, 'child_id');
    }

    public function childPosition() {
        return $this->belongsTo(EmployeePosition::class, 'child_id');
    }
}
