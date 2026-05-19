<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class CloudinaryService
{

    public function __construct()
    {
        // Constructor kosong karena kita menggunakan local storage
    }

    /**
     * Upload foto ke Local Storage (menggantikan Cloudinary)
     * @return string URL foto yang tersimpan
     */
    public function upload(UploadedFile $file, string $folder = 'laporan_lapangan'): string
    {
        $path = $file->store($folder, 'public');
        return asset('storage/' . $path);
    }

    /**
     * Hapus foto dari Local Storage berdasarkan URL
     */
    public function delete(string $url): void
    {
        $baseUrl = asset('storage') . '/';
        if (str_starts_with($url, $baseUrl)) {
            $path = str_replace($baseUrl, '', $url);
            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        }
    }
}
