<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\Operation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['number', 'user', 'tag', 'classe', 'description', 'amount', 'remain', 'total', 'fee', 'file'];

    public $incrementing = false; 
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
            $model->number = '#' . mt_rand(100000, 999999);
        });
    }

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
