<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Illuminate\Auth\Middleware\Authenticate;

class CustomAuthenticate extends Authenticate
{
    use ResponseHelper;
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return $this->sendError('unauthenticated', ['errors' => 'you are not authenticated'], 401);
        }
    }
}
