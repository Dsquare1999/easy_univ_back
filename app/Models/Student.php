<?php

namespace App\Models;

use App\Models\Classe;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Student extends Model
{
    use HasFactory;
    protected $fillable = ['user', 'classe', 'tag', 'file', 'titre', 'statut'];

    public $incrementing = false; 
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
            // $model->user = Auth::id();
        });
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user');
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'tag');
    }

    public function releves(): HasMany
    {
        return $this->hasMany(Releve::class, 'matiere');
    }
}
