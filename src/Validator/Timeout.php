<?php

namespace FvCommunityNews\Validator;

/**
 * Timeout
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Timeout extends AbstractValidator
{
    public function __construct()
    {
        $this->setMessage(__('Timout occured, please resubmit.', 'fvcn'));
    }

    public function isValid($value)
    {
        $time = (int) base64_decode($value);

        // min 10 sec, max 1 hour
        if (($time + 10) > time() || (time() - 3600) > $time) {
            return false;
        }

        return true;
    }
}
