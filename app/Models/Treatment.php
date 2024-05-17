<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function doctor()
    {
        return $this->belongsTo('App\Models\Doctor','doctor_id');
    }
    public function patient()
    {
        return $this->belongsTo('App\Models\Patient','patient_id');
    }
    public function attachments()
    {
        return $this->hasMany('App\Models\Attachment');
    }
    public function payments()
    {
        return $this->hasMany('App\Models\Payment');
    }
}
