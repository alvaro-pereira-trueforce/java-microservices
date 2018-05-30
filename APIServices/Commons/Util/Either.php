<?php

namespace APIServices\Commons\Util;

class Either
{
    protected $error = null;
    protected $success = null;

    /**
     * Either constructor.
     * @param $error
     * @param $success
     */
    public function __construct($error, $success)
    {
        $this->error = $error;
        $this->success = $success;
    }

    /**
     * @param $error
     * @return Either
     */
    public static function errorCreate($error)
    {
        return new Either($error, null);
    }

    /**
     * @param $success
     * @return Either
     */
    public static function successCreate($success)
    {
        return new Either(null, $success);
    }

    /**
     * @return error
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->error != null;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success != null;
    }

    /**
     * @return mixed
     */
    public function success()
    {
        return $this->success;
    }
}