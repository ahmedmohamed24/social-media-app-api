<?php

namespace App\Http\Traits;

trait ApiResponse
{
    public function response($status, $msg, $errors = \null, $data = null)
    {
        return response()->json([
            'data' => $data ?? [],
            'errors' => $errors ?? [],
            'meta' => [
                'message' => $msg,
            ],
        ], $status);
    }
}
