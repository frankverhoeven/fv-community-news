<?php

namespace FvCommunityNews\Admin\Post;

use FvCommunityNews\Post\PostType;

/**
 * Edit
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Edit
{
    /**
     * @var string
     */
    protected $postType;

    /**
     * __construct()
     *
     */
    public function __construct()
    {
        $this->postType = PostType::POST_TYPE_KEY;

        add_action('add_meta_boxes', [$this, 'registerMetaboxPostInfo']);
        add_action('save_post', [$this, 'saveMetaboxPostInfo']);
    }

    /**
     * registerMetaboxPostInfo()
     *
     */
    public function registerMetaboxPostInfo()
    {
        if (empty($_GET['action']) || 'edit' != $_GET['action'] || get_post_type() != $this->postType) {
            return;
        }

        add_meta_box(
            'fvcn_post_info_metabox',
            __('Post Information', 'fvcn'),
            [$this, 'metaboxPostInfo'],
            $this->postType,
            'side',
            'high'
        );

        do_action('fvcn_register_metabox_post_info', get_the_ID());
    }

    /**
     * saveMetaboxPostInfo()
     *
     * @param int $postId
     * @return int
     */
    public function saveMetaboxPostInfo($postId = 0)
    {
        if (empty($postId)) {
            return $postId;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $postId;
        }
        if ('post' != strtolower($_SERVER['REQUEST_METHOD'])) {
            return $postId;
        }
        if (get_post_type($postId) != $this->postType) {
            return $postId;
        }
        if (!current_user_can('edit_posts')) {
            return $postId;
        }

        if (isset($_POST['fvcn_anonymous_author_name']))
            update_post_meta($postId, '_fvcn_anonymous_author_name', $_POST['fvcn_anonymous_author_name']);
        if (isset($_POST['fvcn_anonymous_author_email']))
            update_post_meta($postId, '_fvcn_anonymous_author_email', $_POST['fvcn_anonymous_author_email']);
        if (isset($_POST['fvcn_post_url']))
            update_post_meta($postId, '_fvcn_post_url', apply_filters('fvcn_new_post_pre_url', esc_url(strip_tags($_POST['fvcn_post_url']))));

        do_action('fvcn_save_metabox_post_info', $postId);

        return $postId;
    }

    /**
     * metaboxPostInfo()
     *
     */
    public function metaboxPostInfo()
    {
        $id = get_the_ID();

        if (fvcn_is_post_anonymous($id)) : ?>
            <p>
                <label for="fvcn_post_form_author_name"><?php _e('Author Name', 'fvcn'); ?></label>
                <input type="text" name="fvcn_post_form_author_name" id="fvcn_post_form_author_name" value="<?= get_post_meta($id, '_fvcn_anonymous_author_name', true); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="fvcn_post_form_author_email"><?php _e('Author Email', 'fvcn'); ?></label>
                <input type="text" name="fvcn_post_form_author_email" id="fvcn_post_form_author_email" value="<?= get_post_meta($id, '_fvcn_anonymous_author_email', true); ?>" style="width: 100%;">
            </p>
        <?php endif; ?>

        <p>
            <label for="fvcn_post_url"><?php _e('Link', 'fvcn'); ?></label>
            <input type="text" name="fvcn_post_url" id="fvcn_post_url" value="<?= get_post_meta($id, '_fvcn_post_url', true); ?>" style="width: 100%;">
        </p>
        <!--
        <p>
            <label for="fvcn_post_author_ip"><?php _e('Author IP Address', 'fvcn'); ?></label>
            <input type="text" name="fvcn_post_author_ip" id="fvcn_post_author_ip" value="<?= get_post_meta($id, '_fvcn_author_ip', true); ?>" style="width: 100%;" disabled>
        </p>
        -->
        <p>
            <label for="fvcn_post_rating"><?php _e('Rating', 'fvcn'); ?></label>
            <input type="text" name="fvcn_post_rating" id="fvcn_post_rating" value="<?= get_post_meta($id, '_fvcn_post_rating', true); ?>" style="width: 100%;" disabled>
        </p>

        <?php

        do_action('fvcn_metabox_post_info', $id);
    }
}
