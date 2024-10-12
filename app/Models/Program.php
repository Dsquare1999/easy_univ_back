<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Program extends Model
{
    use HasFactory;
    protected $fillable = [
        'classe', 
        'matiere', 
        'teacher', 
        'day',
        'h_begin',
        'h_end',
        'status',
        'observation',
        'report'
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

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher');
    }

    public function report()
    {
        return $this->belongsTo(Program::class, 'report');
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'matiere');
    }
}
