<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $table = 'visits';
    protected $guarded = [];



    public function visitItems()
    {
        return $this->hasMany(VisitItem::class, 'visit_id', 'id');
    }

    public function agentAttendances()
    {
        return $this->hasOne(AgentAttendance::class, 'visit_id', 'id');
    }

    public function osaVisits()
    {
        return $this->hasMany(OsaVisit::class, 'visit_id', 'id');
    }

    public function returnItems()
    {
        return $this->hasMany(ReturnItem::class, 'visit_id', 'id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

    public function agent()
    {
        return $this->belongsTo(Customer::class, 'agent_id', 'id');
    }

    public function posMaterials()
    {
        return $this->hasMany(PosMaterial::class, 'visit_id', 'id');
    }
}
