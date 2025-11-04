<?php


namespace App\Services\Auth;


use App\Services\BaseService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ResetPasswordService extends BaseService
{
    private $user, $passwordReset;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            $this->setUser();
            $this->setPassword();
            return $this->deletePasswordReset();
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function deletePasswordReset()
    {
        $this->passwordReset = DB::table('password_reset_tokens')->where("token", $this->validatedData['token'])->delete();
        return $this->sendSuccess(null, "password reset successful");
    }

    private function setUser()
    {
        $this->passwordReset = DB::table('password_reset_tokens')->where("token", $this->validatedData['token'])->first();
        $this->user = User::query()->where("email", $this->passwordReset->email)->first();

        if (empty($this->user)) {
            abort(404, 'No user with the token supplied');
        }

        if (Carbon::now()->gt(Carbon::parse($this->passwordReset->created_at)->addMinutes(30))) {
            abort(403, 'Token has expired');
        }
    }

    private function setPassword()
    {
        $this->user->password = bcrypt($this->validatedData["password"]);
        $this->user->save();
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "token" => 'required|integer|min:100000|max:999999|exists:password_reset_tokens,token',
            "password" => 'string|min:6',
        ]);
    }
}
