<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary(
            new Configuration([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name'),
                    'api_key'    => config('cloudinary.api_key'),
                    'api_secret' => config('cloudinary.api_secret'),
                ],
                'url' => [
                    'secure' => true,
                ],
            ])
        );
    }

    /**
     * Upload foto ke Cloudinary
     * @return string URL foto yang tersimpan
     */
    public function upload(UploadedFile $file, string $folder = 'laporan_lapangan'): string
    {
        $result = $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            [
                'folder'        => $folder,
                'resource_type' => 'image',
                'transformation' => [
                    'quality'      => 'auto',
                    'fetch_format' => 'auto',
                ],
            ]
        );

        return (string) $result['secure_url'];
    }

    /**
     * Hapus foto dari Cloudinary berdasarkan URL atau public_id
     */
    public function delete(string $url): void
    {
        // Ekstrak public_id dari URL, mis: .../laporan_lapangan/abc123.jpg → laporan_lapangan/abc123
        $pattern = '/\/v\d+\/(.+)\.[a-z]+$/i';
        if (preg_match($pattern, $url, $matches)) {
            $publicId = $matches[1];
            $this->cloudinary->uploadApi()->destroy($publicId);
        }
    }
}
