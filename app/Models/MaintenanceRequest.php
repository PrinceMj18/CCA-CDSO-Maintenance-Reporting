<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\InventoryUsage;
class MaintenanceRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'ticket_number',
        'title',
        'description',
        'location',
        'category',
        'status',
        'priority',
        'date_completed',
    ];

    protected $casts = [
        'date_completed' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Who created the request
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // Assigned personnel
    public function assignedPersonnel()
{
    return $this->belongsTo(User::class, 'assigned_to')->withTrashed();
}
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function logs()
    {
        return $this->hasMany(RequestLog::class, 'request_id');
    }
    public function assignments()
    {
        return $this->hasMany(RequestAssignment::class, 'request_id');
    }
    /*
    |--------------------------------------------------------------------------
    | Scopes (VERY USEFUL for dashboard)
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function inventoryUsages()
    {
        return $this->hasMany(InventoryUsage::class, 'request_id');
    }

    public function images()
    {
        return $this->hasMany(MaintenanceImage::class, 'maintenance_request_id');
    }

}