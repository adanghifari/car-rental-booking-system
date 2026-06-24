<?php

namespace App\Console\Commands;

use App\Models\Car;
use App\Services\CloudinaryMediaService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MigrateLocalMediaToCloudinary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-to-cloudinary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload existing local car images to Cloudinary and update database paths';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cloudinary = app(CloudinaryMediaService::class);

        if (! $cloudinary->configured()) {
            $this->error('Cloudinary is not configured. Please check your .env credentials.');
            return 1;
        }

        $cars = Car::all();
        $this->info('Starting migration of car images to Cloudinary...');
        $this->info('Total cars to check: ' . $cars->count());

        $mainUploaded = 0;
        $galleryUploaded = 0;

        foreach ($cars as $car) {
            $updated = false;

            // 1. Migrate main image
            if ($car->image && ! $cloudinary->isCloudinaryPath($car->image)) {
                $localPath = storage_path('app/public/' . $car->image);

                if (file_exists($localPath)) {
                    $this->line("Uploading main image for Car ID {$car->id} ({$car->brand} {$car->name})...");
                    
                    try {
                        $file = new UploadedFile($localPath, basename($localPath), null, null, true);
                        $cloudinaryPath = $cloudinary->upload($file, 'cars/main');
                        $car->image = $cloudinaryPath;
                        $mainUploaded++;
                        $updated = true;
                        $this->info("-> Success: {$cloudinaryPath}");
                    } catch (\Exception $e) {
                        $this->error("-> Failed to upload main image: " . $e->getMessage());
                    }
                } else {
                    $this->warn("-> Main image file not found locally: {$car->image}");
                }
            }

            // 2. Migrate gallery images
            if (is_array($car->gallery_images) && count($car->gallery_images) > 0) {
                $newGallery = [];
                $galleryUpdated = false;

                foreach ($car->gallery_images as $imagePath) {
                    if ($imagePath && ! $cloudinary->isCloudinaryPath($imagePath)) {
                        $localPath = storage_path('app/public/' . $imagePath);

                        if (file_exists($localPath)) {
                            $this->line("Uploading gallery image for Car ID {$car->id}...");
                            
                            try {
                                $file = new UploadedFile($localPath, basename($localPath), null, null, true);
                                $cloudinaryPath = $cloudinary->upload($file, 'cars/gallery');
                                $newGallery[] = $cloudinaryPath;
                                $galleryUploaded++;
                                $galleryUpdated = true;
                                $this->info("-> Success: {$cloudinaryPath}");
                            } catch (\Exception $e) {
                                $newGallery[] = $imagePath; // Keep old path on failure
                                $this->error("-> Failed to upload gallery image: " . $e->getMessage());
                            }
                        } else {
                            $newGallery[] = $imagePath; // Keep old path if file not found
                            $this->warn("-> Gallery image file not found locally: {$imagePath}");
                        }
                    } else {
                        $newGallery[] = $imagePath; // Keep existing Cloudinary path
                    }
                }

                if ($galleryUpdated) {
                    $car->gallery_images = $newGallery;
                    $updated = true;
                }
            }

            if ($updated) {
                $car->save();
                $this->info("Car ID {$car->id} updated successfully in database.\n");
            }
        }

        $this->info("Migration completed!");
        $this->info("Main images uploaded: {$mainUploaded}");
        $this->info("Gallery images uploaded: {$galleryUploaded}");

        return 0;
    }
}
