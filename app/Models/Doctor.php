<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Get all of the treatments for the Doctor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class, 'doctor_id');
    }
}
