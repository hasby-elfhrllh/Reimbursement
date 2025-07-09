<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
     use HasFactory;

    protected $fillable = [
        'name',
        'limit_per_month',
    ];

    public function reimbursements()
    {
        return $this->hasMany(Reimbursement::class);
    }
}
