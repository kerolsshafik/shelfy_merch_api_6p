<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentAttendance extends Model
{
    use HasFactory;
    protected $table = 'agent_attendances';
    protected $guarded = [];

    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_id', 'id');
    }
}
