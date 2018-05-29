<?php
namespace APIServices\Commons\Util\Error;

use APIServices\Commons\Tools\TypeError\TypeError;

class Error
{
    /**
     * @var message error
     */
    protected $message;
    /**
     * @var TypeError
     */
    protected $typeError;

    /**
     * Errors constructor.
     * @param $message
     * @param TypeError $typeError
     */
    public function __construct($message, TypeError $typeError)
    {
        $this->message = $message;
        $this->typeError = $typeError;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return TypeError
     */
    public function getTypeError()
    {
        return $this->typeError;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @param TypeError $typeError
     */
    public function setTypeError(TypeError $typeError)
    {
        $this->typeError = $typeError;
    }
}