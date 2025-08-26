<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use App\Models\Classe;
use App\Models\Matiere;

class Unite extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'name', 'slug', 'description'];

    public $incrementing = false; 
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    protected static function booted()
    {
        static::creating(function ($unit) {
            $unit->slug = Str::slug($unit->name);
        });
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe');
    }

    public function matieres()
    {
        return $this->hasMany(Matiere::class, 'unite'); // Changé de 'unites' à 'unite'
    }
}
