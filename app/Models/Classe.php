<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\Cycle;
use App\Models\Filiere;
use App\Models\Matiere;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Classe extends Model
{
    use HasFactory;

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'classe_user', 'classe', 'user');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'classe_tag', 'classe', 'tag');
    }

    public function matieres(): HasMany
    {
        return $this->hasMany(Matiere::class, 'classe', 'id');
    }

    protected $fillable = ['filiere', 'cycle', 'year', 'academic_year', 'parts', 'status'];

    public $incrementing = false; 
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function cycle()
    {
        return $this->belongsTo(Cycle::class, 'cycle');
    }

    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'filiere');
    }

}

