<?php

namespace api\components\responses;

/**
 * Class ErrorResponse
 * @package api\components\responses
 *
 * @rest\model ErrorResponse
 * @rest\property? boolean=false success Weather success was success or not. For error responses it is always `false`
 * @rest\example false
 * @rest\property? int[400,431]=400 code Response status code
 * @rest\example 400
 * @rest\property? object errors Error list
 * @rest\example {"0":"Trips status can't be changed.", "1":"trip_id cannot be blank."}
 */
class ErrorResponse extends ApiResponse
{
    protected $status = 400;

    public function __construct($errors, $status = null)
    {
        $this->setErrors((array) $errors);

        if ($status) {
            $this->setStatus($status);
        }

        parent::__construct();
    }

    public function asArray()
    {
        return [
            'success' => false,
            'code' => $this->getStatus(),
            'errors' => $this->getErrors()
        ];
    }
}
