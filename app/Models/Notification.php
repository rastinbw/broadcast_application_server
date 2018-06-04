<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['content', 'user_id', 'category_id'];

    function user(){
        return $this->belongsTo('App\User');
    }

}
