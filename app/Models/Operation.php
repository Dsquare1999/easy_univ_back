<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Operation extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'montant', 'tag', 'date', 'invoice'];

    public $incrementing = false;  
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
