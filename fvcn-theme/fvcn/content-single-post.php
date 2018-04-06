<?php

/**
 * content-single-post.php
 *
 * Displays a single posts content'
 *
 * @package    FV Community News
 * @subpackage Theme
 */

?>

<div class="entry-content fvcn-post-content">
    <?php if (fvcn_has_post_thumbnail()) : ?>
        <div class="fvcn-post-thumbnail">
            <?php fvcn_post_thumbnail(0, [110, 110]); ?>
        </div>
    <?php endif; ?>

    <?php fvcn_post_content(); ?>

    <?php if (fvcn_has_post_link()) : ?>
        <div class="fvcn-post-link">
            <p><?php printf(__('Read full article: %s', 'fvcn'),
                '<a href="' . fvcn_get_post_link() . '">' . fvcn_get_post_title() . '</a>'); ?></p>
        </div>
    <?php endif; ?>
</div><!-- .entry-content -->

<div class="entry-meta fvcn-post-meta">
    <span class="fvcn-post-tags">
        <?php fvcn_post_tag_list(0, ['before'=>__('Tags: ', 'fvcn'), 'after'=>' | ']); ?>
    </span>
    <span class="fvcn-post-likes">
        <?php fvcn_is_post_liked_by_current_user() ? fvcn_post_unlike_button() : fvcn_post_like_button(); ?>
    </span>
</div><!-- .entry-meta -->
