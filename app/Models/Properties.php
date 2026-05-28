<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Properties extends Model
{
    protected $table = 'property';

    protected $primaryKey = 'propertyno';

    public $incrementing = false;
    protected $keyType = 'string';
}