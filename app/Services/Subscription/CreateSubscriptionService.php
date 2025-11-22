<?php


namespace App\Services\Subscription;


use App\Helpers\PayStackHelper;
use App\Helpers\PhoneHelper;
use App\Models\Subscription;
use App\Models\User;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CreateSubscriptionService extends BaseService
{
    use PhoneHelper;
    private $apiResponse;
    private ?User $user;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            if (!$this->checkAccessKey()) {
                return $this->sendError('access denied');
            }
            $this->setReference();
            $this->callPaystack();
            $this->setEmail();
            $this->getOrCreateUser();
            return $this->createSubscription();
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "reference" => "sometimes|required|string",
            "email" => "sometimes|required|email",
            "phone" => "required|string|max:20|min:10",
            "name" => "required|string|max:80",
            "key" => "required",
            "channel" => "required_if:reference,null|in:paystack,airtime,grant"
        ]);
    }

    private function checkAccessKey()
    {
        return in_array((int)$this->validatedData['key'], [533116,989722,770546]);
    }

    private function callPaystack()
    {
        if (Arr::exists($this->validatedData, 'channel') && $this->validatedData['channel'] == 'paystack')
        {
            $this->apiResponse = (new PayStackHelper([
                'url' => "/transaction/verify/".$this->validatedData['reference'],
                'method' => 'GET',
            ]))->execute();

            abort_unless($this->apiResponse['data'], 503, 'api error');
        }
    }

    private function setEmail()
    {
        if (!Arr::exists($this->validatedData, 'email')) {
            $createdName = '';
            $names = explode(' ',  $this->validatedData['name']);
            foreach ($names as $name) {
                $createdName = $createdName.Str::trim($name);
            }
            $this->validatedData['email'] = $createdName.'@atsamankst.org';
        }
    }

    private function setReference()
    {
        if (!Arr::exists($this->validatedData, 'reference')) {
            $this->validatedData['reference'] = generateReference();
        }
    }

    private function getOrCreateUser()
    {
        $this->validatedData['phone'] = $this->formatNumber($this->validatedData['phone']);
        $this->user = User::query()->firstOrCreate([
            'phone' => $this->validatedData['phone']
        ], [
            'email' => $this->validatedData['email'],
            'name' => $this->validatedData['name'],
            'role' => 'user',
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt('secret'),
        ]);
    }

    private function createSubscription()
    {
        $subscription = Subscription::query()->create([
            'user_id' => $this->user->id,
            'reference' => $this->validatedData['reference'],
            'status' => 'active',
            'code' => Str::lower(Str::random(10)),
            'amount' => round(($this->apiResponse['data']['amount'] / 100), 2),
            'details' => json_encode($this->apiResponse['data'], true),
            'activated_on' => Carbon::now(),
            'expired_on' => Carbon::now()->addYear(),
        ]);

        $token = $this->user->createToken( uniqid(), ['*'], now()->addYear())->plainTextToken;
        $data = array_merge($subscription->toArray(), ['access_token' => $token, 'user' => $this->user]);

        return $this->sendSuccess($data, 'subscription created');
    }

}
