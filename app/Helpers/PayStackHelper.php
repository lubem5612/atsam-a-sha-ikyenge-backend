<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PayStackHelper
{
    use ValidationHelper, ResponseHelper;
    private $httpResponse;
    private $secretKey;
    private $baseUrl;
    private $request;
    private $requestBuilder;
    private $validatedInputs;

    public function __construct(array $request)
    {
        $this->request = $request;
        $this->baseUrl = env('PAYSTACK_BASE_URL', 'https://api.paystack.co');
        $this->secretKey = env('PAYSTACK_SECRET_KEY', null);
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            $this->initializeHttpRequest();
            $this->handleHttpMethods();
            return $this->httpResponse->throw()->json();
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function validateRequest()
    {
        $this->validatedInputs = $this->validate($this->request, [
            'method' => 'required|string|in:POST,PUT,PATCH,GET,DELETE',
            'url' => 'required|string',
            'data' => 'required_unless:method,GET|array'
        ]);
    }

    private function initializeHttpRequest()
    {
        $this->requestBuilder = Http::withHeaders([
            'Authorization' => "Bearer ".$this->secretKey,
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache',
        ])->withoutVerifying();
    }

    private function handleHttpMethods()
    {
        $method = Str::upper($this->validatedInputs['method']);
        $relUrl = Str::start($this->validatedInputs['url'], '/');
        $url = $this->baseUrl.''.$relUrl;

        switch ($method) {
            case "GET": {
                $this->httpResponse = $this->requestBuilder->get($url);
                break;
            }
            case "POST": {
                $this->httpResponse = $this->requestBuilder->post($url, $this->validatedInputs['data']);
                break;
            }
            case "PATCH": {
                $this->httpResponse = $this->requestBuilder->patch($url, $this->validatedInputs['data']);
                break;
            }
            case "PUT": {
                $this->httpResponse = $this->requestBuilder->put($url, $this->validatedInputs['data']);
                break;
            }
            case "DELETE": {
                $this->httpResponse = $this->requestBuilder->delete($url);
                break;
            }
            default: {
                abort(503, 'method not allowed');
                break;
            }
        }
    }
}
