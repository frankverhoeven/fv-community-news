<?php

namespace FvCommunityNews\Validator;

/**
 * Alnum
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Alnum extends AbstractValidator
{
    public function __construct()
    {
        $this->setMessage(__('Value can only contain alphabetic and numeric characters.', 'fvcn'));
    }

    public function isValid($value)
    {
        return (bool) ctype_alnum(str_replace(' ', '', $value));
    }
}
