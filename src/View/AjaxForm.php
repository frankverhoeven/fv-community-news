<?php

namespace FvCommunityNews\View;

use FvCommunityNews\Post\Controller;
use FvCommunityNews\Post\Status;
use WP_Error;

/**
 * AjaxForm
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class AjaxForm
{
    /**
     * @var Controller
     */
    private $postController;
    /**
     * @var WP_Error
     */
    private $error;
    /**
     * @var array
     */
    protected $jsParams = [];

    /**
     * @param Controller $postController
     * @param WP_Error $error
     */
    public function __construct(Controller $postController, WP_Error $error)
    {
        $this->postController = $postController;
        $this->error = $error;

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
     * @return AjaxForm
     */
    public function enqueueScripts()
    {
        $registry = fvcn_container_get('Registry');

        wp_enqueue_script('fvcn-js', $registry['pluginUrl'] . 'public/js/post-form.min.js', ['jquery', 'jquery-form'], false, true);
        wp_localize_script('fvcn-js', 'FvCommunityNewsJavascript', $this->jsParams);

        return $this;
    }

    public function response()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'fvcn-ajax')) {
            exit;
        }

        $postId = $this->postController->createPost();

        if (!empty($this->error->get_error_codes())) {
            $errors = [];
            foreach ($this->error->get_error_codes() as $code) {
                $errors[ $code ] = $this->error->get_error_message($code);
            }

            $response = [
                'success' => 'false',
                'errors' => $errors
            ];
        } else {
            if (Status::publish() == fvcn_get_post_status($postId)) {
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
