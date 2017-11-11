<?php

namespace FvCommunityNews\Validator;

/**
 * Email
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Email extends AbstractValidator
{
    public function __construct()
    {
        $this->setMessage(__('Value must be a valid email address.', 'fvcn'));
    }

    public function isValid($value)
    {
        return (bool) is_email($value);
    }
}
