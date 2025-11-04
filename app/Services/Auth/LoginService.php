<?php


namespace App\Services\Auth;


use App\Services\BaseService;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class LoginService extends BaseService
{
    private ?User $user;
    private $credentials;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            $this->setCredentials();
            return $this->authenticateUser();
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
    }

    private function setCredentials()
    {
        $this->credentials = Arr::only($this->validatedData, ['password', 'email']);
    }

    private function authenticateUser()
    {
       if (Auth::attempt($this->credentials)) {
           $token = Auth::user()->createToken(uniqid())->plainTextToken;
           $this->user = Auth::user();
           $data = Auth::user()->toArray();

           $data = array_merge($data, ['access_token' => $token]);
           request()->session()->regenerate();
           return $this->sendSuccess($data, 'login successful');
       }else {
           if ($this->emailExist())
               $message = 'password does not match email';
           else
               $message = 'records does not exist';
           return $this->sendError($message, [], 401);
       }
    }

    private function emailExist()
    {
        return User::query()->where('email', $this->validatedData['email'])->exists();
    }
}
