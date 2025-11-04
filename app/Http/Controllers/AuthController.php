<?php

namespace App\Http\Controllers;

use App\Services\Auth\ForgotPasswordService;
use App\Services\Auth\GetAuthUserService;
use App\Services\Auth\LoginService;
use App\Services\Auth\LogoutService;
use App\Services\Auth\RegisterService;
use App\Services\Auth\ResetPasswordService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        return (new LoginService($request->all()))->execute();
    }

    public function register(Request $request)
    {
        return (new RegisterService($request->all()))->execute();
    }

    public function logout(Request $request)
    {
        return (new LogoutService())->execute();
    }

    public function forgotPassword(Request $request)
    {
        return (new ForgotPasswordService($request->all()))->execute();
    }

    public function resetPassword(Request $request)
    {
        return (new ResetPasswordService($request->all()))->execute();
    }

    public function user(Request $request)
    {
        return (new GetAuthUserService())->execute();
    }
}
