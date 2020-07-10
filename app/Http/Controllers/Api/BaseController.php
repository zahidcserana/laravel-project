<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;

class BaseController extends Controller
{
    protected $user;
    protected $successMsg = 'Data Successfully saved.';
    protected $errorMsg = 'Something went wrong.';
    protected $errorAccess = 'Access Forbidden!';
    protected $deleteMsg = 'Data successfully deleted.';

    public function __construct()
    {
        $this->order = new User();
    }

    public function sendResponse($data = array(), $message = '')
    {
        $response = [
            'status'  => true,
            'data'    => $data,
            'message' => $message
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $code = 404)
    {
        $response = [
            'status' => false,
            'data'    => [],
            'message' => $error,
        ];

        return response()->json($response, $code);
    }
}
