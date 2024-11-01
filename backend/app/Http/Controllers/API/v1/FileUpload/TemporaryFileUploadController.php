<?php

namespace App\Http\Controllers\API\v1\FileUpload;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileUpload\TemporaryFileRevertRequest;
use App\Http\Requests\FileUpload\TemporaryFileUploadRequest;
use App\Services\FileUpload\TemporaryFileUploadService;
use Illuminate\Http\JsonResponse;

class TemporaryFileUploadController extends Controller
{
    /**
     * The temporary file upload service instance.
     * 
     * @var TemporaryFileUploadService
     */
    private $service;
    
    /**
     * TemporaryFileUploadController contructor.
     * 
     * @param TemporaryFileUploadService $service The instance of TemporaryFileUploadService.
     */
    public function __construct(TemporaryFileUploadService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle user temporary file upload request.
     * 
     * @param App\Http\Requests\FileUpload\TemporaryFileUploadRequest $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function upload(TemporaryFileUploadRequest $request): JsonResponse
    {
        return $this->service->upload($request);
    }

    /**
     * Handle user revert temporary uploaded file request.
     * 
     * @param  App\Http\Requests\FileUpload\TemporaryFileRevertRequest $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function revert(TemporaryFileRevertRequest $request): JsonResponse
    {
        return $this->service->revert($request);
    }
}
