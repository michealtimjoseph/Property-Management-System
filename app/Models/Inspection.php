<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    // 1. Define custom table and keys
    protected $table = 'property_inspection';
    protected $primaryKey = 'inspectionid';
    
    // Assuming your IDs are strings like "INSP-001". 
    // If they are auto-incrementing numbers, change false to true.
    public $incrementing = false; 
    protected $keyType = 'string';
    
    // Since your table doesn't have created_at / updated_at columns
    public $timestamps = false;

    // Allow mass assignment for your form
    protected $fillable = [
        'inspectionid', 'propertyno', 'staffno', 'inspection_date', 'evaluation'
    ];

    // 2. Relationship to Properties model
    public function property()
    {
        // belongsTo(RelatedModel::class, 'foreign_key', 'owner_key')
        return $this->belongsTo(Properties::class, 'propertyno', 'propertyno');
    }

    // 3. Relationship to Staff model
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staffno', 'staffno');
    }
}