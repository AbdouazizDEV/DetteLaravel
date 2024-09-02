<?php

namespace App\Traits;

trait ApiResponse
{
    protected function successResponse($data, $message = "", $statusCode = 200)
    {
        return response()->json([
            'status' => $statusCode,
            'data' => $data,
            'message' => $message
        ], $statusCode);
    }

    protected function errorResponse($message, $statusCode = 400, $data = null)
    {
        return response()->json([
            'status' => $statusCode,
            'data' => $data,
            'message' => $message
        ], $statusCode);
    }
}
