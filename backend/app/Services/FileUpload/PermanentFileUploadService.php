<?php

namespace App\Services\FileUpload;

use App\Models\Profile;
use App\Models\TemporaryFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PermanentFileUploadService
{
    /**
     * Handle user move permanent file request.
     * 
     * @param Illuminate\Http\Request $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function movePermanentFile($userId, $directoryPath)
    {
        try {
            $appendedDirectoryPath = 'uploads/temporary/' . $directoryPath;

            $temporaryFile = TemporaryFile::query()
                            ->where('directory_path', $appendedDirectoryPath)
                            ->first();

            if (empty($directoryPath)) {
                return Profile::query()->where('user_id', $userId)->first()?->avatar;
            }

            if (!$temporaryFile) {
                return;
            }

            $tempDirectoryPath = $temporaryFile->directory_path;
            $fileName = $temporaryFile->file_name;
            $file = $tempDirectoryPath . '/' . $fileName;

            $permanentPath = 'uploads/avatars/' . $userId . '/' . $fileName;

            Storage::disk('public')->deleteDirectory('uploads/avatars/' . $userId);


            Storage::disk('public')->move($file, $permanentPath);

            Storage::disk('public')->deleteDirectory($appendedDirectoryPath);
            

            $temporaryFile->delete();

            return $permanentPath;


        } catch (\Throwable $th) {
            info('Error moving file: ' . $th->getMessage());
        }
    }
}
