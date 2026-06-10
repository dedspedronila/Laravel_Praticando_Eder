<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
    protected $casts = [
        'items' => 'array'
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime', // ISSO AQUI resolve o seu problema!
            'items' => 'array',   // Garante que o array de infraestrutura funcione também
        ];
    }

    protected $guarded = [];

    protected $dates = ['date'];


    public function user() {
        return $this->belongsTo('App\Models\User');
    }

     public function users() {
        return $this->belongsToMany('App\Models\User');
    }

    }
