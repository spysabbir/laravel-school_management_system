<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Teacher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'designation_id',
        'national_id_no',
        'joining_date',
        'type',
        'status',
        'resignation_date',
        'retirement_date',
        'suspension_date',
        'resignation_reason',
        'suspension_reason',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }
}
