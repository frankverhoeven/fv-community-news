<?php

namespace FvCommunityNews\Validator;

/**
 * Url
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Url extends AbstractValidator
{
    public function __construct()
    {
        $this->setMessage(__('Value must be a valid url.', 'fvcn'));
    }

    public function isValid($value)
    {
        return (bool) filter_var($value, FILTER_VALIDATE_URL);
    }
}
