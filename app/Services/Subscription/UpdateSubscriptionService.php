<?php


namespace App\Services\Subscription;


use App\Models\Subscription;
use App\Services\BaseService;
use Illuminate\Support\Str;

class UpdateSubscriptionService extends BaseService
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
            $this->setSubscription();
            return $this->updateSubscription();
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function setSubscription()
    {
        $this->subscription = Subscription::query()->find($this->validatedData['id']);
    }

    private function updateSubscription()
    {
        $this->subscription->fill($this->validatedData)->save();
        return $this->sendSuccess($this->subscription->refresh(), 'subscription updated');
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "id" => "required|exists:subscriptions,id",
            'status' => 'sometimes|required|string|in:active,inactive',
            'merchant' => 'sometimes|required|string',
            'device_id' => 'sometimes|required|string',
        ]);
    }
}
