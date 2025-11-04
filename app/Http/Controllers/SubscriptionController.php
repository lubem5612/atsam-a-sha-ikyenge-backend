<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\Subscription\CreateSubscriptionService;
use App\Services\Subscription\DeleteSubscriptionService;
use App\Services\Subscription\SearchSubscriptionService;
use App\Services\Subscription\UpdateSubscriptionService;
use App\Services\Subscription\VerifySubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        return (new SearchSubscriptionService(Subscription::class, []))->execute();
    }

    public function store(Request $request)
    {
        $key = $request->query('key');
        return (new CreateSubscriptionService($request->merge(['key' => $key])->all()))->execute();
    }

    public function show($id)
    {
        return (new SearchSubscriptionService(Subscription::class, [], $id))->execute();
    }

    public function update(Request $request, $id)
    {
        return (new UpdateSubscriptionService($request->merge(['id' => $id])->all()))->execute();
    }

     public function verify(Request $request)
    {
        return (new VerifySubscriptionService($request->all()))->execute();
    }

    public function destroy($id)
    {
        return (new DeleteSubscriptionService(['id' => $id]))->execute();
    }
}
