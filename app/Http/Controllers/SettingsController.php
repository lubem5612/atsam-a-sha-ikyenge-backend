<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    use ResponseHelper;

    public function paystack()
    {
        $config = [
            'secret_key' => env('PAYSTACK_SECRET_KEY'),
            'public_key' => env('PAYSTACK_PUBLIC_KEY'),
            'base_url' => env('PAYSTACK_BASE_URL'),
        ];

        return $this->sendSuccess($config, 'paystack configurations retrieved');
    }
}
