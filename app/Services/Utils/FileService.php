<?php

namespace App\Services\Utils;

use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FileService
{
    /**
     * Store a file in the given directory.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return string
     */
    public function store(UploadedFile $file, string $directory): string
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = date('Ymd_His') . '_' . Str::random(8) . '.' . $extension;
        $path = $file->storeAs($directory , $fileName, 'public');

        return $path;
    }

    /**
     * Delete a file from the storage.
     *
     * @param string $path The path to the file
     */
    public function delete(string $path): void
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
