<?php

namespace FvCommunityNews\Validator;

/**
 * MaxLength
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class MaxLength extends AbstractValidator
{
    protected $_maxLength;

    public function __construct($maxLength=250)
    {
        $this->_maxLength = abs((int) $maxLength);
        $this->setMessage(sprintf(__('Value cannot have more then %d characters.' , 'fvcn'), $this->_maxLength));
    }

    public function isValid($value)
    {
        return (bool) (mb_strlen($value) <= $this->_maxLength);
    }
}
