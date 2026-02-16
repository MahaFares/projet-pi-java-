<?php

namespace App\Service;

use Cloudinary\Cloudinary;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CloudinaryService
{
    private $cloudinary;

    public function __construct(string $cloudinaryUrl)
    {
        $this->cloudinary = new \Cloudinary\Cloudinary($cloudinaryUrl);
    }

    public function uploadImage(UploadedFile $file, string $folder = 'ecotrip'): string
    {
        $upload = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
            'folder' => $folder,
        ]);

        return $upload['secure_url'];
    }

    public function deleteImage(string $publicId): void
    {
        $this->cloudinary->uploadApi()->destroy($publicId);
    }
}
