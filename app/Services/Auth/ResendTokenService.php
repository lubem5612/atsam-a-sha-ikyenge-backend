<?php


namespace App\Services\Auth;


use App\Mail\VerificationMail;
use App\Services\BaseService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ResendTokenService extends BaseService
{
    private $user, $token;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            $this->setUser();
            $this->setToken();
            $this->saveToken();
            $this->handleNotification();
            return $this->sendSuccess(null, 'token resend successfully');
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function setToken()
    {
        $this->token = rand(100000, 999999);
    }

    private function setUser()
    {
        $this->user = User::query()->where('email', $this->validatedData['email'])->first();
    }

    private function saveToken()
    {
        if (!empty($this->user->email_verified_at)) {
            throw new \Exception('user already verified', 403);
        }
        $this->user->update([
            "token" => $this->token,
            "email_verified_at" => Carbon::now()
        ]);
    }

    private function handleNotification()
    {
        Mail::to($this->user->email)->send(new VerificationMail($this->user));
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "email" => 'required|exists:users,email'
        ]);
    }
}
