<?php


namespace App\Services\User;


use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Arr;

class UpdateUserService extends BaseService
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
            $this->setUser();
            $this->setRole();
            return $this->updateUser();
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function setUser()
    {
        $this->user = User::query()->find($this->validatedData['user_id']);
    }

    private function setRole()
    {
        if (Arr::exists($this->request, 'role') && $this->request['role'] && isAdmin()) {
            $this->validatedData['role'] = $this->request['role'];
        }
    }

    private function updateUser()
    {
        $this->user->fill($this->validatedData)->save();
        return $this->sendSuccess($this->user->refresh(), 'user updated successfully');
    }

    private function validateRequest()
    {
        $data = $this->validate($this->request, [
            'name' => 'required|string|max:180',
            'email' => 'required|email|unique:users,email',
            'role' => 'sometimes|required|in:0,1',
        ]);
        $this->validatedData = Arr::except($data, ['role']);
    }
}
