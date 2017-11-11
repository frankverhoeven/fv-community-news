<?php

namespace FvCommunityNews\View;

use FvCommunityNews\Container;
use FvCommunityNews\Post\PostType;
use FvCommunityNews\Registry;

/**
 * AjaxForm
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class AjaxForm
{
    /**
     * @var array
     */
    protected $jsParams = [];

    /**
     * __construct()
     *
     * @version 20120712
     */
    public function __construct()
    {
        $this->jsParams = [
            'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
            'nonce' => wp_create_nonce('fvcn-ajax'),
            'action' => 'fvcn-ajax',
            'thumbnail' => fvcn_is_post_form_thumbnail_enabled() ? '1' : '0',
            'locale' => [
                'loading' => __('Loading', 'fvcn')
            ]
        ];

        add_action('wp_ajax_fvcn-ajax', [$this, 'response']);
        add_action('wp_ajax_nopriv_fvcn-ajax', [$this, 'response']);
    }

    /**
     * enqueueScripts()
     *
     * @version 20120717
     * @return AjaxForm
     */
    public function enqueueScripts()
    {
        // Replace the outdated version of jQuery Form that is shipped with WordPress
        wp_deregister_script('jquery-form');
        wp_register_script('jquery-form', Registry::get('pluginUrl') . 'public/js/jquery-form.js');

        wp_enqueue_script('fvcn-js', Registry::get('pluginUrl') . 'public/js/fvcn-js.js', ['jquery', 'jquery-form']);

        wp_localize_script('fvcn-js', 'FvCommunityNewsJavascript', $this->jsParams);

        return $this;
    }

    public function response()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'fvcn-ajax')) {
            exit;
        }

        $postId = fvcn_new_post_handler();

        if (fvcn_has_errors()) {
            $errors = [];
            foreach (Container::getInstance()->getWpError()->get_error_codes() as $code) {
                $errors[ $code ] = Container::getInstance()->getWpError()->get_error_message($code);
            }

            $response = [
                'success' => 'false',
                'errors' => $errors
            ];
        } else {
            if (PostType::STATUS_PUBLISH == fvcn_get_post_status($postId)) {
                $permalink = fvcn_get_post_permalink($postId);
                $message = '';
            } else {
                $permalink = '';
                $message = __('Your post has been added and is pending review.', 'fvcn');
            }

            $response = [
                'success' => 'true',
                'permalink' => $permalink,
                'message' => $message
            ];
        }

        die(json_encode($response));
    }
}
