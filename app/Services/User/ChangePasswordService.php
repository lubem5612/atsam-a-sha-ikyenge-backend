<?php


namespace App\Services\User;


use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordService extends BaseService
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
            $this->getUserById();
            $this->checkPassword();
            $this->updatePassword();
            return $this->sendSuccess($this->user->refresh(), 'password changed successfully');
        } catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'new_password' => 'required|string|min:6',
            'old_password' => 'required|string|min:6',
            'user_id' => 'required|exists:users,id',
        ]);
    }

    private function getUserById()
    {
        $this->user = User::query()->find($this->validatedData['user_id']);
        abort_if(empty($this->user), 401, 'user not found');
    }

    private function checkPassword()
    {
        if (Auth::id() == $this->validatedData['user_id']) {
            $matchedPassword = Hash::check($this->validatedData['old_password'], $this->user->password);
            abort_unless($matchedPassword, 404, 'old password does not match');
        }elseif ((Auth::id() != $this->validatedData['user_id']) && !isAdmin()) {
            abort(403, 'only admin can reset a user password');
        }
    }

    private function updatePassword()
    {
        $this->user->password = bcrypt($this->validatedData['new_password']);
        $this->user->save();
    }
}
