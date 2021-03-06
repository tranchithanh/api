<?php

namespace App\Ship\Exceptions\Formatters;

use Apiato\Core\Exceptions\Formatters\ExceptionsFormatter as CoreExceptionsFormatter;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Class AuthorizationExceptionFormatter
 *
 * @author Johannes Schobel <johannes.schobel@googlemail.com>
 * @author Mahmoud Zalt  <mahmoud@zalt.me>
 */
class AuthorizationExceptionFormatter extends CoreExceptionsFormatter
{

    /**
     * Status Code.
     *
     * @var integer
     */
    const STATUS_CODE = 403;

    /**
     * @param \Exception                    $exception
     * @param \Illuminate\Http\JsonResponse $response
     *
     * @return  array
     */
    public function responseData(Exception $exception, JsonResponse $response)
    {
        $response;
        return [
            'code'    => $exception->getCode(),
            'message' => $exception->getMessage(),
            'errors'      => 'You have no access to this resource!',
            'status_code' => self::STATUS_CODE,
        ];
    }

    /**
     * @param \Exception                    $exception
     * @param \Illuminate\Http\JsonResponse $response
     *
     * @return  mixed
     */
    public function modifyResponse(Exception $exception, JsonResponse $response)
    {
        $exception;
        return $response;
    }

    /**
     * @return  int
     */
    public function statusCode()
    {
        return self::STATUS_CODE;
    }
}
