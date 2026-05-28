<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    // 1. Tell Laravel the exact table name
    protected $table = 'property';

    // 2. Tell Laravel the primary key is 'propertyno', not 'id'
    protected $primaryKey = 'propertyno';

    // 3. Tell Laravel the primary key is a string, not an auto-incrementing number
    public $incrementing = false;
    protected $keyType = 'string';

    // 4. If your table doesn't have 'created_at' and 'updated_at' columns, add this:
    public $timestamps = false; 
}