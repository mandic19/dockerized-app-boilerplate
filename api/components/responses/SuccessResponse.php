<?php

namespace api\components\responses;

/**
 * Class SuccessResponse
 * @package api\components\responses
 *
 * @rest\model SuccessResponse
 * @rest\property? boolean=true success Weather success was success or not. For success responses it is always `true`
 * @rest\example true
 * @rest\property? int[200,210]=200 code Response status code
 * @rest\example 200
 * @rest\property? object data Response data. Response specific data
 *
 *
 */
class SuccessResponse extends ApiResponse
{
    protected $status = 200;

    public function __construct($data, $status = null)
    {
        $this->setData($data);

        if ($status) {
            $this->setStatus($status);
        }

        parent::__construct();
    }

    public function asArray()
    {
        $data = [];
        $data['success'] = true;
        $data['code'] = $this->getStatus();
        $data['data'] = $this->getSerializedData();

        return $data;
    }
}
