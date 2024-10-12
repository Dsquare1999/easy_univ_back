<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

use App\Models\Student;
use App\Models\Matiere;

class Releve extends Model
{
    use HasFactory;
    protected $fillable = [
        'classe', 
        'matiere', 
        'student', 
        'exam1',
        'observation_exam1',
        'exam2',
        'observation_exam2',
        'partial',
        'observation_partial',
        'remedial',
        'observation_remedial',
        'status'
    ];

    public $incrementing = false; 
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'matiere');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student');
    }
}
