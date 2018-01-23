<?php

namespace FvCommunityNews\Post;

use FvCommunityNews\Config\WordPress as Config;
use FvCommunityNews\Validator\Email;
use FvCommunityNews\Validator\Image;
use FvCommunityNews\Validator\MaxLength;
use FvCommunityNews\Validator\MinLength;
use FvCommunityNews\Validator\Name;
use FvCommunityNews\Validator\NotEmpty;
use FvCommunityNews\Validator\Tags;
use FvCommunityNews\Validator\Url;
use FvCommunityNews\Validator\ValidatorChain;
use WP_Error;

/**
 * Validator
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Validator
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var WP_Error
     */
    private $error;
    /**
     * @var array
     */
    private $validators;
    /**
     * @var array
     */
    private $data;
    /**
     * @var string
     */
    private $messageTemplate = '<strong>ERROR</strong>: %s';

    /**
     * @param Config $config
     * @param WP_Error $error
     */
    public function __construct(Config $config, WP_Error $error)
    {
        $this->config = $config;
        $this->error = $error;
    }

    /**
     * Validate post, returns true if all fields are valid.
     *
     * @param array $data Post data
     * @return bool
     */
    public function isValid(array $data): bool
    {
        $this->messageTemplate = __($this->messageTemplate, 'fvcn');

        $this->data = [];

        $validators = $this->getValidators();
        if (!fvcn_is_anonymous()) {
            unset($validators['fvcn_post_form_author_name'], $validators['fvcn_post_form_author_email']);
        }

        foreach ($validators as $field => $fieldValidators) {
            if (!$this->isEnabled($field)) continue;

            $validator = new ValidatorChain($fieldValidators);
            $value = isset($data[$field]) ? $data[$field] : null;

            if (!$this->isRequired($field)) {
                $notEmpty = new NotEmpty();

                if ('fvcn_post_form_thumbnail' == $field) {
                    $value = $value['tmp_name'];
                }

                if ($notEmpty->isValid($value)) {
                    if ('fvcn_post_form_thumbnail' == $field) {
                        $value = $data[$field];
                    }

                    if ($validator->isValid($value)) {
                        $this->data[$field] = $this->filterField($field, $value);
                    } else {
                        $this->error->add($field, sprintf($this->messageTemplate, $validator->getMessage()));
                    }
                }
            } else {
                if ($validator->isValid($value)) {
                    $this->data[$field] = $this->filterField($field, $value);
                } else {
                    $this->error->add($field, sprintf($this->messageTemplate, $validator->getMessage()));
                }
            }
        }

        return empty($this->error->get_error_codes());
    }

    /**
     * Setup post validation
     *
     * @return array
     */
    protected function getValidators(): array
    {
        if (null === $this->validators) {
            $validators = [
                'fvcn_post_form_author_name' => [
                    NotEmpty::class,
                    Name::class,
                    new MinLength($this->config['_fvcn_post_form_author_name_length_min']),
                    new MaxLength($this->config['_fvcn_post_form_author_name_length_max']),
                ],
                'fvcn_post_form_author_email' => [
                    NotEmpty::class,
                    Email::class,
                    new MaxLength(300),
                ],
                'fvcn_post_form_title' => [
                    NotEmpty::class,
                    new MinLength($this->config['_fvcn_post_form_title_length_min']),
                    new MaxLength($this->config['_fvcn_post_form_title_length_max']),
                ],
                'fvcn_post_form_link' => [
                    NotEmpty::class,
                    Url::class,
                    new MinLength($this->config['_fvcn_post_form_link_length_min']),
                    new MaxLength($this->config['_fvcn_post_form_link_length_max']),
                ],
                'fvcn_post_form_content' => [
                    NotEmpty::class,
                    new MinLength($this->config['_fvcn_post_form_content_length_min']),
                    new MaxLength($this->config['_fvcn_post_form_content_length_max']),
                ],
                'fvcn_post_form_tags' => [
                    NotEmpty::class,
                    Tags::class,
                    new MinLength($this->config['_fvcn_post_form_tags_length_min']),
                    new MaxLength($this->config['_fvcn_post_form_tags_length_max']),
                ],
                'fvcn_post_form_thumbnail' => [
                    Image::class,
                ],
            ];

            $this->validators = apply_filters('fvcn_post_form_validators', $validators);
        }

        return $this->validators;
    }

    /**
     * Checks if the provided field is enabled.
     *
     * @param string $field
     * @return bool
     */
    protected function isEnabled(string $field): bool
    {
        $enabled = true;
        $key = '_' . $field . '_enabled';

        if (isset($this->config[$key]) && false === (bool) $this->config[$key]) {
            $enabled = false;
        }

        return $enabled;
    }

    /**
     * Checks if the provided field is required.
     *
     * @param string $field
     * @return bool
     */
    protected function isRequired(string $field): bool
    {
        $required = true;
        $key = '_' . $field . '_required';
        if (isset($this->config[$key]) && false === (bool) $this->config[$key]) {
            $required = false;
        }

        return $required;
    }

    /**
     * Apply filters to a field value.
     *
     * @param string $field
     * @param string|null $value
     * @return string|null
     */
    protected function filterField(string $field, $value)
    {
        $field = str_replace('_fvcn_', '', $field);
        return apply_filters('fvcn_new_post_pre_' . $field, $value);
    }

    /**
     * Get validated data
     *
     * @return array
     */
    public function getData(): array
    {
        if (null === $this->data) {
            return [];
        }
        return $this->data;
    }
}
