<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'fee'];

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
        static::creating(function ($tag) {
            $tag->slug = Str::slug($tag->name);
        });
    }
}
