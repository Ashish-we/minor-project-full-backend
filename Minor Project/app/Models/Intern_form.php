<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intern_form extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'description',
        'pdf',
        'job_id',
    ];
}
