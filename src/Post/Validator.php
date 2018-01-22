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

        $data = array_merge([
            'fvcn_post_form_author_name' => null,
            'fvcn_post_form_author_email' => null,
            'fvcn_post_form_title' => null,
            'fvcn_post_form_link' => null,
            'fvcn_post_form_content' => null,
            'fvcn_post_form_tags' => null,
            'fvcn_post_form_thumbnail' => null,
        ], $data);

        if (fvcn_is_anonymous()) {
            $validator = apply_filters('fvcn_post_author_name_validators', new ValidatorChain([
                NotEmpty::class,
                Name::class,
                new MinLength(2),
                new MaxLength(40)
            ]));

            if (!$validator->isValid($data['fvcn_post_form_author_name'])) {
                $this->error->add('fvcn_post_form_author_name', sprintf($this->messageTemplate, $validator->getMessage()));
            }

            $validator = apply_filters('fvcn_post_author_email_validators', new ValidatorChain([
                NotEmpty::class,
                Email::class,
                new MinLength(10),
                new MaxLength(60)
            ]));

            if (!$validator->isValid($data['fvcn_post_form_title'])) {
                $this->error->add('fvcn_post_form_title', sprintf($this->messageTemplate, $validator->getMessage()));
            }
        }

        $validator = apply_filters('fvcn_post_title_validators', new ValidatorChain([
            NotEmpty::class,
            new MinLength(8),
            new MaxLength(70)
        ]));

        if (!$validator->isValid($data['fvcn_post_form_title'])) {
            $this->error->add('fvcn_post_form_title', sprintf($this->messageTemplate, $validator->getMessage()));
        }

        if ($this->config['_fvcn_post_form_link_required']) {
            $validator = apply_filters('fvcn_post_link_validators', $validator->setValidators([
                NotEmpty::class,
                Url::class,
                new MinLength(6),
                new MaxLength(1000),
            ]));

            if (!$validator->isValid($data['fvcn_post_form_link'])) {
                $this->error->add('fvcn_post_form_link', sprintf($this->messageTemplate, $validator->getMessage()));
            }
        } else {
            $notEmpty = new NotEmpty();
            if ($notEmpty->isValid($data['fvcn_post_form_link'])) {
                $validator = apply_filters('fvcn_post_link_validators', $validator->setValidators([
                    NotEmpty::class,
                    Url::class,
                    new MinLength(6),
                    new MaxLength(1000),
                ]));

                if (!$validator->isValid($data['fvcn_post_form_link'])) {
                    $this->error->add('fvcn_post_form_link', sprintf($this->messageTemplate, $validator->getMessage()));
                }
            }
        }

        $validator = apply_filters('fvcn_post_content_validators', new ValidatorChain([
            NotEmpty::class,
            new MinLength(20),
            new MaxLength(5000),
        ]));

        if (!$validator->isValid($data['fvcn_post_form_content'])) {
            $this->error->add('fvcn_post_form_content', sprintf($this->messageTemplate, $validator->getMessage()));
        }

        if ($this->config['_fvcn_post_form_tags_required']) {
            $validator = apply_filters('fvcn_post_tags_validators', new ValidatorChain([
                NotEmpty::class,
                Tags::class,
                new MinLength(2),
                new MaxLength(1000),
            ]));

            if (!$validator->isValid($data['fvcn_post_form_tags'])) {
                $this->error->add('fvcn_post_form_tags', sprintf($this->messageTemplate, $validator->getMessage()));
            }
        } else {
            $notEmpty = new NotEmpty();
            if ($notEmpty->isValid($data['fvcn_post_form_tags'])) {
                $validator = apply_filters('fvcn_post_tags_validators', new ValidatorChain([
                    Tags::class,
                    new MinLength(2),
                    new MaxLength(1000),
                ]));

                if (!$validator->isValid($data['fvcn_post_form_tags'])) {
                    $this->error->add('fvcn_post_form_tags', sprintf($this->messageTemplate, $validator->getMessage()));
                }
            }
        }

        if ($this->config['_fvcn_post_form_thumbnail_required']) {
            $validator = apply_filters('fvcn_post_title_validators', new ValidatorChain([
                Image::class,
            ]));

            if (!$validator->isValid($data['fvcn_post_form_thumbnail'])) {
                $this->error->add('fvcn_post_form_thumbnail', sprintf($this->messageTemplate, $validator->getMessage()));
            } else {
                add_action('fvcn_insert_post', [fvcn_container_get(Mapper::class), 'insertPostThumbnail']);
                // @todo: change
            }
        } else if (!empty($data['fvcn_post_form_thumbnail']['tmp_name'])) {
            $validator = apply_filters('fvcn_post_title_validators', new ValidatorChain([
                Image::class,
            ]));

            if (!$validator->isValid($data['fvcn_post_form_thumbnail'])) {
                $this->error->add('fvcn_post_form_thumbnail', sprintf($this->messageTemplate, $validator->getMessage()));
            } else {
                add_action('fvcn_insert_post', [fvcn_container_get(Mapper::class), 'insertPostThumbnail']);
                // @todo: change
            }
        }

        return empty($this->error->get_error_codes());
    }
}
