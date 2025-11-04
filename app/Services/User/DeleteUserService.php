<?php


namespace App\Services\User;


use App\Models\User;
use App\Services\BaseService;

class DeleteUserService extends BaseService
{
    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            $this->deleteUser();
            return $this->sendSuccess(null, "user deleted");
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "id" => "required|exists:users,id"
        ]);
    }

    private function deleteUser()
    {
        User::destroy($this->validatedData['id']);
    }
}
