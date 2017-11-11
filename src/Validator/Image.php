<?php

namespace FvCommunityNews\Validator;

/**
 * Image
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Image extends AbstractValidator
{
    public function __construct()
    {
        $this->setMessage(__('The image provided is not a valid image.', 'fvcn'));
    }

    public function isValid($value)
    {
        if (empty($value['tmp_name'])) {
            $this->setMessage(__('Value cannot be empty.', 'fvcn'));
            return false;
        }

        if (UPLOAD_ERR_OK != $value['error']) {
            return false;
        }

        if (filesize($value['tmp_name']) < 12) {
            return false;
        }

        $valid = [
            'gif' => IMAGETYPE_GIF,
            'png' => IMAGETYPE_PNG,
            'jpg' => IMAGETYPE_JPEG,
            'jpe' => IMAGETYPE_JPEG,
            'jpeg' => IMAGETYPE_JPEG
        ];

        if (!array_key_exists(strtolower(pathinfo($value['name'], PATHINFO_EXTENSION)), $valid)) {
            return false;
        }

        $mime = exif_imagetype($value['tmp_name']);

        if (!$mime || !in_array($mime, $valid)) {
            return false;
        }

        return true;
    }
}
