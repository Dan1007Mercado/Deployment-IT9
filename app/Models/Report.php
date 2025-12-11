<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type', 
        'start_date',
        'end_date',
        'file_path',
        'file_content', // Add this
        'file_size',    // Add this
        'mime_type',    // Add this
        'generated_by'  // Add this
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'file_content' => 'array' // Store as JSON array
    ];

    // Relationship to user who generated the report
    public function generatedByUser()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
    
    // Check if report has file content
    public function hasFileContent()
    {
        return !empty($this->file_content) && isset($this->file_content['data']);
    }
    
    // Get decoded PDF content
    public function getPdfContent()
    {
        if ($this->hasFileContent()) {
            return base64_decode($this->file_content['data']);
        }
        return null;
    }
    
    // Get report type label
    public function getTypeLabelAttribute()
    {
        $labels = [
            'revenue' => 'Revenue Report',
            'occupancy' => 'Occupancy Report',
            'guest' => 'Guest Report',
            'financial' => 'Financial Report'
        ];
        
        return $labels[$this->type] ?? ucfirst($this->type);
    }
}