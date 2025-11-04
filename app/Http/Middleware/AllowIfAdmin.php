<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AllowIfAdmin
{
    use ResponseHelper;
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isAdmin = !empty($request->user()) && request()->user()->role == 'admin';
        if ($isAdmin) {
            return $next($request);
        }

        return $this->sendError('you must be an admin to continue');
    }
}
