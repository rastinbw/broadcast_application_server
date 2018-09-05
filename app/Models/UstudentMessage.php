<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UstudentMessage extends Model
{
    protected $table = 'ustudent_message';

    protected $fillable = ['message_id', 'ustudent_id', 'user_id'];


}
