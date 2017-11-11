<?php

namespace FvCommunityNews\Validator;

/**
 * AbstractValidator
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
abstract class AbstractValidator
{
    /**
     * Validation message
     * @var string
     */
    protected $message = '';

    /**
     * setMessage()
     *
     * @version 20120704
     * @param string $message
     * @return AbstractValidator
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * getMessage()
     *
     * @version 20120704
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * validate()
     *
     * @param mixed $value
     * @return bool
     */
    abstract public function isValid($value);
}
