<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    // BaseController.php (sketch)
    protected function success($data = [], string $msg = null, int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $msg,
            'data'    => $data,
        ], $code);
    }

    protected function error(array|string $errors, string $msg = null, int $code = 422)
    {
        return response()->json([
            'success' => false,
            'message' => $msg,
            'errors'  => (array) $errors,
        ], $code);
    }

}
