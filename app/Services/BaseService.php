<?php


namespace App\Services;


use App\Helpers\ResponseHelper;
use App\Helpers\ValidationHelper;

class BaseService
{
    use ValidationHelper, ResponseHelper;

    public $request, $validatedData;
}
