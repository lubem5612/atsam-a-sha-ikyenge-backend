<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::any('/', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => "welcome to Atsam a NKST base url ".$request->url(),
        'data' => [
            'request' => array_merge($request->all(), ['path' => $request->getPathInfo()]),
            'authorization' => $request->header('Authorization'),
            'user_agent' => $request->userAgent(),
            'has_session' => $request->hasPreviousSession(),
        ]
    ]);
});
