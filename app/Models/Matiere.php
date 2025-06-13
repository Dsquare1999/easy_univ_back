<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

use App\Models\Releve;
use App\Models\Program;
use App\Models\Classe;
use App\Models\User;
use App\Models\Unite;
use Illuminate\Support\Str;

class Matiere extends Model
{
    use HasFactory;
    protected $fillable = ['unite', 'name', 'code', 'libelle', 'hours', 'classe', 'teacher', 'coefficient', 'year_part'];

    public $incrementing = false; 
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher');
    }

    public function releves(): HasMany
    {
        return $this->hasMany(Releve::class, 'matiere');
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'matiere');
    }

    public function unite()
    {
        return $this->belongsTo(Unite::class, 'unite');
    }
}
