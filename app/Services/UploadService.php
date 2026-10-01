<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UploadService
{
    /**
     * Upload a file directly to the public/uploads directory.
     *
     * @param UploadedFile $file
     * @param string $folder Relative path under public/uploads, e.g. 'users', 'laporan/123/kerusakan'
     * @return string Relative path stored in database, e.g. 'uploads/users/filename.ext'
     */
    public static function upload(UploadedFile $file, string $folder): string
    {
        // Make sure the path is clean
        $folder = trim($folder, '/');
        
        // Target directory: public/uploads/{folder}
        $targetDir = public_path('uploads/' . $folder);
        
        // Ensure folder exists
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true, true);
        }
        
        // Generate unique filename
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        
        // Move file to target directory
        $file->move($targetDir, $filename);
        
        // Return relative path for database
        return 'uploads/' . $folder . '/' . $filename;
    }

    /**
     * Replace an old file with a new file.
     *
     * @param string|null $oldPath Relative path of the old file from database, e.g. 'uploads/users/filename.ext'
     * @param UploadedFile $newFile The new file to upload
     * @param string $folder Relative folder path under public/uploads
     * @return string Relative path of the new file
     */
    public static function replace(?string $oldPath, UploadedFile $newFile, string $folder): string
    {
        if ($oldPath) {
            self::delete($oldPath);
        }

        return self::upload($newFile, $folder);
    }

    /**
     * Delete a file from public/uploads.
     *
     * @param string|null $relativePath Relative path from database, e.g. 'uploads/users/filename.ext'
     * @return bool
     */
    public static function delete(?string $relativePath): bool
    {
        if (!$relativePath) {
            return false;
        }

        $fullPath = public_path($relativePath);

        if (File::exists($fullPath) && File::isFile($fullPath)) {
            return File::delete($fullPath);
        }

        return false;
    }

    /**
     * Delete an entire directory inside public/uploads.
     *
     * @param string $folder Relative folder path under public/uploads, e.g. 'laporan'
     * @return bool
     */
    public static function deleteDirectory(string $folder): bool
    {
        $targetDir = public_path('uploads/' . trim($folder, '/'));

        if (File::isDirectory($targetDir)) {
            return File::deleteDirectory($targetDir);
        }

        // Fallback check if $folder is the actual path (e.g., config('harbang.foto_path') which is 'laporan-foto')
        $fallbackDir = public_path(trim($folder, '/'));
        if (File::isDirectory($fallbackDir)) {
            return File::deleteDirectory($fallbackDir);
        }

        return false;
    }
}
