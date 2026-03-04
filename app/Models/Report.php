<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type', 
        'start_date',
        'end_date',
        'file_path',
        'file_size',
        'mime_type',
        'generated_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    // Relationship to user who generated the report
    public function generatedByUser()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
    
    // Check if report exists in S3
    public function existsInS3()
    {
        return $this->file_path && Storage::disk('s3')->exists($this->file_path);
    }
    
    // Get temporary S3 URL for viewing/download
    public function getS3Url($minutes = 5)
    {
        if ($this->file_path && $this->existsInS3()) {
            return Storage::disk('s3')->temporaryUrl($this->file_path, now()->addMinutes($minutes));
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
    
    // Format file size
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size ?? 0;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    // Auto-delete from S3 when report is deleted
    protected static function booted()
    {
        static::deleting(function ($report) {
            if ($report->file_path && Storage::disk('s3')->exists($report->file_path)) {
                Storage::disk('s3')->delete($report->file_path);
            }
        });
    }
}