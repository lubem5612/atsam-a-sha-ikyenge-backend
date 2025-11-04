<?php


namespace App\Services\Subscription;


use App\Models\Subscription;
use App\Services\BaseService;

class DeleteSubscriptionService extends BaseService
{
    private ?Subscription $subscription;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            $this->validateRequest();;
            return $this->deleteSubscription();
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function deleteSubscription()
    {
        Subscription::destroy($this->validatedData['id']);
        return $this->sendSuccess(null, 'subscription deleted');
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "id" => "required|exists:subscriptions,id",
        ]);
    }
}
