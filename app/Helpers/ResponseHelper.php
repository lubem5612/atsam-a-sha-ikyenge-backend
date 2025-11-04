<?php


namespace App\Helpers;


use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait ResponseHelper
{
    /**
     * success response method.
     *
     * @param $result
     * @param $message
     * @param string $status
     * @return Response
     */
    public function sendSuccess($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200, [], JSON_INVALID_UTF8_SUBSTITUTE );
    }

    /**
     * return error response.
     *
     * @param $error
     * @param array $errorMessages
     * @param int $code
     * @return Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
            'data' => null
        ];

        if(!empty($errorMessages)){
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code, [], JSON_INVALID_UTF8_SUBSTITUTE );
    }

    /**
     * structured response returned for methods
     *
     * @param $message
     * @param bool $success
     * @param $data
     * @param int $code
     * @return mixed
     */
    public function buildResponse($message, $success=false, $data=null, $code=500)
    {
        return [
            "message" => $message,
            "success" => $success,
            "data" => $data,
            "code" => $code,
        ];
    }

    public function sendServerError(\Exception $exception, $code=500)
    {
        Log::error($exception); // Don't remove. Logs error to slack on server

        if (Str::contains($exception->getTraceAsString(), 'ValidationHelper.php')) {
            $code = 422;
            $response = [
                "success" => false,
                "message" => "Validation error",
                "data" => null,
                "errors" => json_decode($exception->getMessage()),
            ];
        }else {
            $response = [
                "success" => false,
                "message" => $exception->getMessage(),
                "data" => null,
                "errors" => $this->formatServerError($exception),
            ];
        }
        if (config('app.env') == 'local') Log::error($exception->getTraceAsString());

        return response()->json($response, $code, [], JSON_INVALID_UTF8_SUBSTITUTE );
    }

    private function formatServerError(\Exception $e)
    {
        $errors = [];
        foreach ($e->getTrace() as $error) {
            if (array_key_exists('line', $error ) && $error['line'] && array_key_exists('file', $error) && $error['file']) {
                $message = "error on line {$error['line']} in the file {$error['file']}";
                array_push($errors, $message);
            }
        }
        return $errors;
    }

    private function isValidationError($exception)
    {
        $errorASString = implode(',', $this->formatServerError($exception));
        return Str::contains($errorASString, 'ValidationHelper.php');
    }
}
