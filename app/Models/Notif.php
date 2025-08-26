<?php

namespace App\Models;

use App\Models\User;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Notif extends Model
{
    protected $fillable = ['user', 'type', 'title', 'message', 'is_read'];

    public $incrementing = false;  
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}