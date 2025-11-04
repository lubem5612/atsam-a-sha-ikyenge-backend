<?php


namespace App\Services\Auth;


use App\Mail\PasswordResetMail;
use App\Services\BaseService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordService extends BaseService
{
    private $user, $token, $message;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            $this->setUser();
            $this->generateCode();
            $this->deleteResetIfExists();
            $this->createPasswordReset();
            $this->sendNotification();
            return $this->sendSuccess($this->token, $this->message);
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function setUser()
    {
        $this->user = User::query()->where("email", $this->validatedData["email"])->first();
    }

    private function createPasswordReset()
    {
        DB::table('password_reset_tokens')->insert([
            "email" => $this->user->email,
            "token" => $this->token,
            "created_at" => Carbon::now()
        ]);

        $this->message = "A password reset token has been sent to your email";
    }

    private function generateCode()
    {
        $this->token = rand(100000, 999999);
        $this->user->token = $this->token;
        $this->user->save();
    }

    private function deleteResetIfExists()
    {
        if (DB::table('password_reset_tokens')->where('email', $this->user->email)->exists()) {
            DB::table('password_reset_tokens')->where('email', $this->user->email)->delete();
        }
    }

    private function sendNotification()
    {
        Mail::to($this->user->email)->send(new PasswordResetMail($this->user));
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "email" => 'string|email|exists:users,email'
        ]);
    }
}
