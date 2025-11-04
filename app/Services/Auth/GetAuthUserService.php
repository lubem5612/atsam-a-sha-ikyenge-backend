<?php


namespace App\Services\Auth;


use App\Services\BaseService;
use Illuminate\Support\Facades\Auth;

class GetAuthUserService extends BaseService
{
    public function execute()
    {
        try {
            $user = Auth::user();
            return $this->sendSuccess($user, 'authenticated user retrieved');
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }
}
