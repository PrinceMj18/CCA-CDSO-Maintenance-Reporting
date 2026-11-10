<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class RequestAssignment extends Model
{
    protected $fillable = [
        'request_id',
        'personnel_id',
        'assigned_by',
        'assigned_at',
    ];
protected $casts = [
    'assigned_at' => 'datetime',
];
    public function request()
    {
        return $this->belongsTo(MaintenanceRequest::class, 'request_id');
    }


    public function personnel()
{
    return $this->belongsTo(User::class, 'personnel_id')->withTrashed();
}

    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class, 'request_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
