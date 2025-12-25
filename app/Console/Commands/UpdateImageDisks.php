<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class UpdateImageDisks extends Command
{
    protected $signature = 'images:update-disks';
    protected $description = 'Update disk column for existing images based on file location';

    public function handle()
    {
        $images = Image::whereNull('disk')->orWhere('disk', '')->get();
        $this->info("Found {$images->count()} images to update");

        $updated = 0;
        $s3Count = 0;
        $publicCount = 0;

        foreach ($images as $image) {
            // Check if file exists on S3
            if (Storage::disk('s3')->exists($image->filepath)) {
                $image->disk = 's3';
                $s3Count++;
            } 
            // Check if file exists in public/uploads
            elseif (file_exists(public_path($image->filepath)) || file_exists(public_path('uploads/' . $image->filename))) {
                $image->disk = 'public';
                $publicCount++;
            }
            // Check if file exists in storage/app/public
            elseif (Storage::disk('public')->exists($image->filepath)) {
                $image->disk = 'public';
                $publicCount++;
            }
            else {
                // Default to public if file not found
                $image->disk = 'public';
                $publicCount++;
                $this->warn("File not found for image {$image->id}: {$image->filepath}");
            }

            $image->save();
            $updated++;
        }

        $this->info("Updated {$updated} images");
        $this->info("S3: {$s3Count}, Public: {$publicCount}");
        
        return 0;
    }
}
