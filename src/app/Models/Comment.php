<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'profile_id', 
        'item_id',
        'content',
    ];

    public function profile(){
        return $this->belongsTo(Profile::class);
    }

    public function item(){
        return $this->belongsTo(Item::class);
    }
}
