<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /** @const string */
    const RESPONSE_SUCCESS = 'success';

    /** @const string */
    const RESPONSE_ERROR = 'error';

    /** @var null */
    protected $data = null;

    /** @var null */
    protected $errorMessage = null;

    /** @var string */
    protected $responseType;



    /**
     * Build the response.
     *
     * @param int $statusCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function returnResponse($statusCode = Response::HTTP_OK)
    {
        $response = [
            'responseType' => $this->responseType,
            'data' => $this->data,
            'errorMessage' => $this->errorMessage
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Return not found error.
     *
     * @param null $errorMessage
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function returnNotFound($errorMessage = null)
    {
        $this->responseType = self::RESPONSE_ERROR;
        $this->errorMessage = $errorMessage ? $errorMessage : 'Entity not found!';

        return $this->returnResponse();
    }

    /**
     * Return bad request error.
     *
     * @param null $errorMessage
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function returnBadRequest($errorMessage = null)
    {
        $this->responseType = self::RESPONSE_ERROR;
        $this->errorMessage = $errorMessage ? $errorMessage : 'Bad request';

        return $this->returnResponse();
    }

    /**
     * Return unknown error.
     *
     * @param null $errorMessage
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function returnError($errorMessage = null)
    {
        $this->responseType = self::RESPONSE_ERROR;
        $this->errorMessage = $errorMessage ? $errorMessage : 'Error!!!';

        return $this->returnResponse();
    }

    /**
     * Return success.
     *
     * @param null $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function returnSuccess($data = null)
    {
        $this->responseType = self::RESPONSE_SUCCESS;
        $this->data = $data;

        return $this->returnResponse();
    }
}



