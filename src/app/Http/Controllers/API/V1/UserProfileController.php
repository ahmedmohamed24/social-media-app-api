<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Traits\ApiResponse;

class UserProfileController extends Controller
{
    use ApiResponse;

    public function show()
    {
        $profile = \auth()->user()->profile;

        return $this->response(200, 'success', \null, ['profile' => $profile]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        \auth('api')->user()->profile()->update($request->validated());

        return $this->response(200, 'success', \null, ['profile' => \auth('api')->user()->profile]);
    }
}
