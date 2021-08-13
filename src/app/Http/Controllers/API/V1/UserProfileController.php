<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Traits\ApiResponse;

class UserProfileController extends Controller
{
    use ApiResponse;

    public function show()
    {
        $profile = \auth()->user()->profile;

        return $this->response(200, 'success', \null, ['profile' => ProfileResource::make($profile)]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        \auth('api')->user()->profile()->update($request->validated());

        $profile = \auth('api')->user()->profile;

        return $this->response(200, 'success', \null, ['profile' => ProfileResource::make($profile)]);
    }
}
