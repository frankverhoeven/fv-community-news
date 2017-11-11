<?php

namespace FvCommunityNews\Validator;

/**
 * Name
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Name extends AbstractValidator
{
    public function __construct()
    {
        $this->setMessage(__('Value can only contain letters.', 'fvcn'));
    }

    public function isValid($value)
    {
        return (bool) preg_match('/^[\p{L}\p{M}]+$/u', str_replace(' ', '', $value));
    }
}
