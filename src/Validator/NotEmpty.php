<?php

namespace FvCommunityNews\Validator;

/**
 * NotEmpty
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class NotEmpty extends AbstractValidator
{
    protected $trim;

    public function __construct($trim = true)
    {
        $this->trim = (bool) $trim;
        $this->setMessage(__('Value cannot be empty.', 'fvcn'));
    }

    public function isValid($value)
    {
        if ($this->trim) {
            $value = trim($value);
        }

        if (empty($value) || '' == $value || null == $value) {
            return false;
        }

        return true;
    }
}
