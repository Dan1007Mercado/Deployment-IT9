<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class S3ReportService
{
    protected $disk;
    protected $bucket;
    
    public function __construct()
    {
        $this->disk = Storage::disk('s3');
        $this->bucket = env('AWS_BUCKET');
    }
    
    /**
     * Upload PDF to S3
     */
    public function uploadPdf($pdfContent, $reportName, $reportType, $startDate, $endDate)
    {
        try {
            // Generate unique filename
            $filename = $this->generateFilename($reportName, $reportType, $startDate, $endDate);
            
            // Define S3 path (organize by type/date)
            $s3Key = 'reports/' . $reportType . '/' . date('Y/m/d') . '/' . $filename;
            
            // Upload to S3
            $this->disk->put($s3Key, $pdfContent, [
                'ContentType' => 'application/pdf',
                'ContentDisposition' => 'inline; filename="' . $filename . '"',
                'Metadata' => [
                    'generated_at' => now()->toDateTimeString(),
                    'report_type' => $reportType,
                    'report_name' => $reportName
                ]
            ]);
            
            Log::info('PDF uploaded to S3', [
                's3_key' => $s3Key,
                'bucket' => $this->bucket,
                'size' => strlen($pdfContent)
            ]);
            
            return [
                's3_key' => $s3Key,
                'file_size' => strlen($pdfContent),
                'file_path' => $s3Key
            ];
            
        } catch (\Exception $e) {
            Log::error('S3 upload failed: ' . $e->getMessage(), [
                'report_name' => $reportName,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Generate temporary URL for download/view
     */
    public function getTemporaryUrl($s3Key, $minutes = 5)
    {
        try {
            if (!$this->disk->exists($s3Key)) {
                Log::warning('S3 file not found for temporary URL', ['s3_key' => $s3Key]);
                return null;
            }
            
            return $this->disk->temporaryUrl($s3Key, now()->addMinutes($minutes));
            
        } catch (\Exception $e) {
            Log::error('Failed to generate temporary URL: ' . $e->getMessage(), [
                's3_key' => $s3Key
            ]);
            return null;
        }
    }
    
    /**
     * Delete from S3
     */
    public function deletePdf($s3Key)
    {
        try {
            if ($this->disk->exists($s3Key)) {
                $result = $this->disk->delete($s3Key);
                Log::info('Deleted from S3', ['s3_key' => $s3Key]);
                return $result;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Failed to delete from S3: ' . $e->getMessage(), [
                's3_key' => $s3Key
            ]);
            return false;
        }
    }
    
    /**
     * Check if file exists
     */
    public function exists($s3Key)
    {
        try {
            return $this->disk->exists($s3Key);
        } catch (\Exception $e) {
            Log::error('Failed to check S3 existence: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get file size
     */
    public function getSize($s3Key)
    {
        try {
            if ($this->exists($s3Key)) {
                return $this->disk->size($s3Key);
            }
        } catch (\Exception $e) {
            Log::error('Failed to get S3 file size: ' . $e->getMessage());
        }
        
        return 0;
    }
    
    /**
     * List all reports in S3 (for admin)
     */
    public function listReports($prefix = 'reports/', $maxKeys = 100)
    {
        try {
            $files = $this->disk->files($prefix);
            return array_slice($files, 0, $maxKeys);
        } catch (\Exception $e) {
            Log::error('Failed to list S3 files: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generate unique filename
     */
    protected function generateFilename($reportName, $reportType, $startDate, $endDate)
    {
        $cleanName = Str::slug($reportName);
        $dateRange = $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d');
        $uniqueId = Str::random(8);
        $timestamp = now()->format('Ymd_His');
        
        return "{$cleanName}_{$dateRange}_{$timestamp}_{$uniqueId}.pdf";
    }
}