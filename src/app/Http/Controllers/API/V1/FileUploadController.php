<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FileUploadController extends Controller
{
    use ApiResponse;
    protected $fileUploaderService;

    public function __construct(FileUploadService $fileUploaderService)
    {
        $this->fileUploaderService = $fileUploaderService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files' => ['required', 'array', 'max:5'], //max to upload 5 files
            'files.*' => ['file', 'mimes:png,jpg,pdf,svg,md', 'max:1024'],
        ]);
        if ($validator->fails()) {
            return $this->response(401, 'invalid given data', $validator->getMessageBag(), $request->all());
        }
        $response = $this->fileUploaderService->upload();

        return $this->response(201, 'uploaded', \null, $response->toArray());
    }
}
