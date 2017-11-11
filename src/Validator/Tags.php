<?php

namespace FvCommunityNews\Validator;

/**
 * Tags
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Tags extends AbstractValidator
{
    public function __construct()
    {
        $this->setMessage(__('Value can only contain alphabetic characters.', 'fvcn'));
    }

    public function isValid($value)
    {
        return (bool) preg_match('/^[a-zA-Z, ]+$/', $value);
    }
}
