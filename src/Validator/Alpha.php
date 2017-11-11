<?php

namespace FvCommunityNews\Validator;

/**
 * Alpha
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Alpha extends AbstractValidator
{
    public function __construct()
    {
        $this->setMessage(__('Value can only contain alphabetic characters.', 'fvcn'));
    }

    public function isValid($value)
    {
        return (bool) ctype_alpha(str_replace(' ', '', $value));
    }
}
