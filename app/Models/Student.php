<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'birth_reg_no',
        'registration_no',
        'father_name',
        'mother_name',
        'guardian_name',
        'guardian_relation',
        'guardian_phone',
        'guardian_email',
        'guardian_address',
        'status',
        'graduation_date',
        'transfer_date',
        'dropout_date',
        'suspension_date',
        'expulsion_date',
        'transfer_reason',
        'suspension_reason',
        'expulsion_reason',
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
}
