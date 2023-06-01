<?php

namespace App\Models;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Attributes\SearchUsingPrefix;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
class job_form extends Model
{
    use HasFactory, Searchable;
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'company_id',
        'skills',
    ];
    protected $primaryKey = 'job_id';

    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'skills' => $this->skills,
        ]; 
    }
}
