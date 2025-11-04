<?php


namespace App\Services\Auth;


use App\Services\BaseService;
use Illuminate\Support\Facades\Auth;

class LogoutService extends BaseService
{
    private $user, $log;

    public function execute()
    {
        try {
            $this->setUser();
            $this->logoutUser();
            return $this->sendSuccess(null, 'user logged out successfully');
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function setUser()
    {
        $this->user = Auth::user();
    }

    private function logoutUser()
    {
        $this->user->tokens()->delete();
        request()->session()->regenerate();
    }
}
