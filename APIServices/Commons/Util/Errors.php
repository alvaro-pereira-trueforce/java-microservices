<?php

namespace APIServices\Commons\Util;


class Errors
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * Errors constructor.
     * @param Error $error
     */
    public function __construct(Error $error)
    {
        $this->addError($error);
    }

    /**
     * @param Error $error
     */
    public function addError(Error $error)
    {
        if (empty($this->errors)) {
            $this->errors = [];
        } else {
            array_push($this->errors, $error);
        }
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->errors);
    }

    /**
     * @param Errors $errors
     */
    public function addAll(Errors $errors)
    {
        $errorList = $errors->getErrors();
        foreach ($errorList as $e) {
            array_push($this->errors, $e);
        }
    }
}