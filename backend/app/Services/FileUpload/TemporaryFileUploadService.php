<?php

namespace App\Services\FileUpload;

use App\Models\TemporaryFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TemporaryFileUploadService
{
    /**
     * Handle user temporary file upload request.
     * 
     * @param Illuminate\Http\Request $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function upload($request): JsonResponse
    {
        try {
            $file = $request->file('file');

            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $uuid = (string) Str::uuid();
            $fileName = $uuid . '.' . $extension;

            $directoryPath = 'uploads/temporary/' . $uuid;

            $file->storeAs($directoryPath, $fileName, 'public');

            TemporaryFile::create([
                'original_name' => $originalName,
                'file_name' => $fileName,
                'directory_path' => $directoryPath
            ]);

            return response()->json(['file_path' => $uuid], 200);
        } catch(ValidationException $e) {
            info("Validation Error on user temporary file upload: " . $e->getMessage());
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            info('Error storing to temporary file' . $th->getMessage());
            return response()->json(['message' => 'An error occurred during temporary file upload.'], 500);
        }
    }

    /**
     * Handle user revert temporary uploaded file request.
     * 
     * @param Illuminate\Http\Request $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function revert($request)
    {
        try {
            $temporaryDirectoryPath = 'uploads/temporary/' . $request->file_path;
            
            if (Storage::disk('public')->exists($temporaryDirectoryPath)) {
                Storage::disk('public')->deleteDirectory($temporaryDirectoryPath);
    
                TemporaryFile::where('directory_path', $temporaryDirectoryPath)->delete();
    
                return response()->json(['message' => 'Directory And File Removed Successfully.'], 200);
            }
            
            return response()->json(['error' => 'Directory Not Found.'], 404);
        } catch(ValidationException $e) {
            info("Validation Error on user temporary file revert: " . $e->getMessage());
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            info('Error storing to temporary file' . $th->getMessage());
            return response()->json(['message' => 'An error occurred during temporary file revert.'], 500);
        }
        
    }
}
