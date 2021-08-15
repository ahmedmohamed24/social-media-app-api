<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileUploaderStoreRequest;
use App\Http\Traits\ApiResponse;
use App\Services\FileUploadService;

class FileUploadController extends Controller
{
    use ApiResponse;
    protected $fileUploaderService;

    public function __construct(FileUploadService $fileUploaderService)
    {
        $this->fileUploaderService = $fileUploaderService;
    }

    public function store(FileUploaderStoreRequest $request)
    {
        $response = $this->fileUploaderService->upload();

        return $this->response(201, 'uploaded', \null, $response->toArray());
    }
}
