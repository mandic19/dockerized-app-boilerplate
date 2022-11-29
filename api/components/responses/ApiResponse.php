<?php

namespace api\components\responses;

use yii\base\BaseObject;

class ApiResponse extends BaseObject
{
    /**
     * @var integer Response code
     */
    protected $status = 200;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array errors
     */
    protected $errors = [];

    /**
     * @return integer
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param integer $status
     *
     * @return $this
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSerializedData()
    {
        $serializer = new \yii\rest\Serializer();

        return $serializer->serialize($this->data);
    }

    /**
     * @return mixed
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData($data): self
    {
        $this->data = $data ? : [];

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     *
     * @return $this
     */
    public function setErrors(array $errors = []): self
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Append error to the list.
     *
     * @param array $error
     *
     * @return self
     */
    public function addError(array $error = []): self
    {
        if (!$this->hasErrors()) {
            return $this->setErrors($error);
        }

        $this->setErrors(
            array_merge($this->getErrors(), $error)
        );

        return $this;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return $this->getErrors() ? true : false;
    }
}
