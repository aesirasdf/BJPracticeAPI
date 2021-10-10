<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function Parent(){
        return $this->belongsTo(Post::class);
    }

    public function Comments(){
        return $this->hasMany(Post::class);
    }
}
