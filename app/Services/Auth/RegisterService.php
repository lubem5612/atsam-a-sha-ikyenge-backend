<?php


namespace App\Services\Auth;


use App\Services\BaseService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class RegisterService extends BaseService
{
    private ?User $user;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            $this->setPassword();
            $this->setUserRole();
            $this->setVerificationDetails();
            $this->createUser();
            return $this->sendSuccess($this->user, 'account created');
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'name' => 'required|string|max:180',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'sometimes|required|string|in:user,admin',
        ]);
    }

    private function setPassword()
    {
        $this->validatedData['password'] = bcrypt($this->validatedData['password']);
    }

    private function setVerificationDetails()
    {
        $this->validatedData['email_verified_at'] = Carbon::now();
        $this->validatedData['token'] = rand(100000, 999999);
    }

    private function setUserRole()
    {
        if (!Arr::exists($this->validatedData, 'role')) {
            $this->validatedData['role'] = 'user';
        }
    }

    private function createUser()
    {
        $user = User::query()->create($this->validatedData);
        $token = $user->createToken(uniqid())->plainTextToken;

        $data = $user->toArray();
        $this->user = array_merge($data, ['access_token' => $token]);
    }
}
