<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Traits\ApiResponse;
use App\Services\FileUploadService;

class UserProfileController extends Controller
{
    use ApiResponse;
    protected $fileUploaderService;

    public function __construct(FileUploadService $fileUploaderService)
    {
        $this->fileUploaderService = $fileUploaderService;
    }

    public function show()
    {
        $profile = \auth()->user()->profile;

        return $this->response(200, 'success', \null, ['profile' => ProfileResource::make($profile)]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $profile = \auth('api')->user()->profile;
        if ($request->input('media')) {
            $this->fileUploaderService->saveProfilePicture($request->media, $profile);
        }
        $profile->update($request->validated());
        $profile = \auth('api')->user()->profile;

        return $this->response(200, 'success', \null, ['profile' => ProfileResource::make($profile)]);
    }
}
