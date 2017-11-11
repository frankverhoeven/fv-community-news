<?php

namespace FvCommunityNews\Validator;

/**
 * MinLength
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class MinLength extends AbstractValidator
{
    protected $_minLength;

    public function __construct($minLength=10)
    {
        $this->_minLength = abs((int) $minLength);
        $this->setMessage(sprintf(__('Value requires at least %d characters.' , 'fvcn'), $this->_minLength));
    }

    public function isValid($value)
    {
        return (bool) (mb_strlen($value) >= $this->_minLength);
    }
}
