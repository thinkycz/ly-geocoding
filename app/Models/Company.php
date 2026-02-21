<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'title',
        'company_id',
        'street',
        'city',
        'latitude',
        'longitude',
        'color',
    ];
}
