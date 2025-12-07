<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class TicketTransition extends Model
{
    use HasFactory, BelongsToOrganization;
    protected $fillable = ['from_state', 'to_state','ticket_id','date','status'];
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

}
