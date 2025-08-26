<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\Cycle;
use App\Models\Filiere;
use App\Models\Matiere;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Document extends Model
{
    use HasFactory;

    protected $fillable = ['classe', 'student', 'tag', 'name', 'path', 'type'];

    public $incrementing = false; 
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

}

