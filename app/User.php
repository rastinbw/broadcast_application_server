<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function students()
    {
        return $this->hasMany('App\Models\Student');
    }

    public function workbooks()
    {
        return $this->hasMany('App\Models\Workbook');
    }

    public function tickets()
    {
        return $this->hasMany('App\Models\Ticket');
    }

    public function posts()
    {
        return $this->hasMany('App\Models\Post');
    }

    public function medias()
    {
        return $this->hasMany('App\Models\Media');
    }
}
