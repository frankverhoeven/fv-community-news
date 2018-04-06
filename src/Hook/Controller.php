<?php

namespace FvCommunityNews\Hook;

use FvCommunityNews\Post\Controller as PostController;

/**
 * Controller
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Controller implements HookInterface
{
    /**
     * @var PostController
     */
    private $postController;

    /**
     * @param PostController $postController
     */
    public function __construct(PostController $postController)
    {
        $this->postController = $postController;
    }

    /**
     * Execute the hook
     *
     * @return void
     */
    public function doHook()
    {
        if ('post' == strtolower($_SERVER['REQUEST_METHOD'])) {
            if (isset($_POST['fvcn_post_form_action']) && 'fvcn-new-post' == $_POST['fvcn_post_form_action']) {
                $this->postController->createPost();
            }
            if (isset($_POST['fvcn-post-like-action'], $_POST['fvcn-post-id'])) {
                $this->postController->likeUnlikePost();
            }
        }
    }
}
