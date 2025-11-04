<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\User\DeleteUserService;
use App\Services\User\SearchUserService;
use App\Services\User\UpdateUserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return (new SearchUserService(User::class, []))->execute();
    }

    public function show($id)
    {
        return (new SearchUserService(User::class, [], $id))->execute();
    }

    public function update(Request $request, $id)
    {
        return (new UpdateUserService($request->merge(['id' => $id])->all()))->execute();
    }

    public function destroy($id)
    {
        return (new DeleteUserService(['id' => $id]))->execute();
    }
}
