<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use Notifiable;

    protected $table = 'staff';

    protected $primaryKey = 'staffno';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'staffno', 'firstname', 'lastname', 'address', 
        'telephoneno', 'sex', 'date_of_birth', 'nin', 
        'position', 'salary', 'date_joined', 'branchno', 
        'password', 'email'
    ];

    protected $hidden = ['password', 'remember_token']; 

    protected $casts = [
        'password' => 'hashed',
        'date_of_birth' => 'date',
        'date_joined' => 'date',
    ];
    
}
