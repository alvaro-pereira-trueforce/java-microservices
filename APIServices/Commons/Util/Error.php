<?php
namespace APIServices\Commons\Util;

class Error
{
    /**
     * @var string
     */
    protected $message;
    /**
     * @var int
     */
    protected $typeError;

    /**
     * Errors constructor.
     * @param $message
     * @param $typeError
     */
    public function __construct($message, $typeError)
    {
        $this->message = $message;
        $this->typeError = $typeError;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getTypeError()
    {
        return $this->typeError;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @param int $typeError
     */
    public function setTypeError($typeError)
    {
        $this->typeError = $typeError;
    }
}