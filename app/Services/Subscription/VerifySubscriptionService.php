<?php


namespace App\Services\Subscription;


use App\Models\Subscription;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class VerifySubscriptionService extends BaseService
{
    private ?Subscription $subscription;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            $this->getSubscription();
            $this->checkUsage();
            $this->updateExpiryDate();
            $this->updateSubscription();
            return $this->sendSuccess($this->subscription->refresh(), 'subscription verified');
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'code' => 'required|string|size:10|exists:subscriptions,code',
            'device_id' => 'sometimes|required|string',
        ]);
    }

    private function getSubscription()
    {
        $this->subscription = Subscription::query()->where('code', $this->validatedData['code'])->first();
        if (Carbon::now()->gt(Carbon::parse($this->subscription->expired_on))) {
            abort(403, 'subscription has expired');
        }
    }

    private function checkUsage()
    {
        if (Arr::exists($this->validatedData, 'device_id') && $this->validatedData['device_id']) {
            if (!empty($this->subscription->device_id)
                && $this->validatedData['device_id'] != $this->subscription->device_id
            ) {
                abort(401, 'code already used on another device');
            }
        }
    }

    private function updateExpiryDate()
    {
        if ($this->subscription->number_used == 0) {
            $this->validatedData['activated_on'] = Carbon::now();
            $this->validatedData['expired_on'] = Carbon::now()->addYear();
        }
    }

    private function updateSubscription()
    {
        $this->validatedData['number_used'] = $this->subscription->number_used + 1;
        $this->validatedData['status'] = 'active';
        $this->subscription->fill($this->validatedData)->save();
    }
}
