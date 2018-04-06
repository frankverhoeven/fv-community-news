<?php

/**
 * form-post.php
 *
 * The form for submitting community news.
 *
 * @package    FV Community News
 * @subpackage Theme
 */

?>

<?php if (fvcn_is_post_added()): ?>

<?php do_action('fvcn_post_added_before'); ?>
<div class="fvcn-post-added">
    <?php if (fvcn_is_post_added_approved()): ?>
        <p><?php _e('Your post has been added.', 'fvcn'); ?></p>
    <?php else: ?>
        <p><?php _e('Your post has been added and is pending review.', 'fvcn'); ?></p>
    <?php endif; ?>
</div>
<?php do_action('fvcn_post_added_after'); ?>

<?php else: ?>

<?php do_action('fvcn_post_form_before'); ?>

<div class="fvcn-post-form">
    <div class="fvcn-error">
        <?php fvcn_post_form_field_error('fvcn_post_form'); ?>
    </div>

    <form class="fvcn-post-form-new-post" method="post" action=""<?php if (fvcn_is_post_form_thumbnail_enabled()): ?> enctype="multipart/form-data"<?php endif; ?>>
        <?php do_action('fvcn_post_form_extras_top'); ?>

        <?php if (fvcn_is_anonymous()): ?>

            <?php do_action('fvcn_post_form_before_author_name'); ?>
            <div class="fvcn-post-form-author-name">
                <label for="fvcn_post_form_author_name"><?php fvcn_post_form_author_name_label(); ?></label>
                <input type="text" name="fvcn_post_form_author_name" id="fvcn_post_form_author_name" value="<?php fvcn_post_form_author_name(); ?>">
                <div class="fvcn-error">
                    <?php fvcn_post_form_field_error('fvcn_post_form_author_name'); ?>
                </div>
            </div>
            <?php do_action('fvcn_post_form_after_author_name'); ?>

            <?php do_action('fvcn_post_form_before_author_email'); ?>
            <div class="fvcn-post-form-author-email">
                <label for="fvcn_post_form_author_email"><?php fvcn_post_form_author_email_label(); ?></label>
                <input type="text" name="fvcn_post_form_author_email" id="fvcn_post_form_author_email" value="<?php fvcn_post_form_author_email(); ?>">
                <div class="fvcn-error">
                    <?php fvcn_post_form_field_error('fvcn_post_form_author_email'); ?>
                </div>
            </div>
            <?php do_action('fvcn_post_form_after_author_email'); ?>

        <?php else: ?>

            <div class="fvcn-post-form-author-logged-in">
                <?php printf(__('Currently logged in as <a href="%1$s">%2$s</a>. <a href="%3$s">Log out</a>', 'fvcn'), admin_url('profile.php'), fvcn_get_current_user_name(), wp_logout_url(apply_filters('the_permalink', get_permalink(home_url('/'))))); ?>
            </div>

        <?php endif; ?>

        <?php do_action('fvcn_post_form_before_title'); ?>
        <div class="fvcn-post-form-title">
            <label for="fvcn_post_form_title"><?php fvcn_post_form_title_label(); ?></label>
            <input type="text" name="fvcn_post_form_title" id="fvcn_post_form_title" value="<?php fvcn_post_form_title(); ?>">
            <div class="fvcn-error">
                <?php fvcn_post_form_field_error('fvcn_post_form_title'); ?>
            </div>
        </div>
        <?php do_action('fvcn_post_form_after_title'); ?>

        <?php if (fvcn_is_post_form_link_enabled()): ?>

            <?php do_action('fvcn_post_form_before_link'); ?>
            <div class="fvcn-post-form-link">
                <label for="fvcn_post_form_link"><?php fvcn_post_form_link_label(); ?></label>
                <input type="text" name="fvcn_post_form_link" id="fvcn_post_form_link" value="<?php fvcn_post_form_link(); ?>">
                <div class="fvcn-error">
                    <?php fvcn_post_form_field_error('fvcn_post_form_link'); ?>
                </div>
            </div>
            <?php do_action('fvcn_post_form_after_link'); ?>

        <?php endif; ?>

        <?php do_action('fvcn_post_form_before_content'); ?>
        <div class="fvcn-post-form-content">
            <label for="fvcn_post_form_content"><?php fvcn_post_form_content_label(); ?></label>
            <textarea name="fvcn_post_form_content" id="fvcn_post_form_content" rows="3"><?php fvcn_post_form_content(); ?></textarea>
            <div class="fvcn-error">
                <?php fvcn_post_form_field_error('fvcn_post_form_content'); ?>
            </div>
        </div>
        <?php do_action('fvcn_post_form_after_content'); ?>

        <?php if (fvcn_is_post_form_tags_enabled()): ?>

            <?php do_action('fvcn_post_form_before_tags'); ?>
            <div class="fvcn-post-form-tags">
                <label for="fvcn_post_form_tags"><?php fvcn_post_form_tags_label(); ?></label>
                <input type="text" name="fvcn_post_form_tags" id="fvcn_post_form_tags" value="<?php fvcn_post_form_tags(); ?>">
                <div class="fvcn-error">
                    <?php fvcn_post_form_field_error('fvcn_post_form_tags'); ?>
                </div>
            </div>
            <?php do_action('fvcn_post_form_after_tags'); ?>

        <?php endif; ?>

        <?php if (fvcn_is_post_form_thumbnail_enabled()): ?>

            <?php do_action('fvcn_post_form_before_thumbnail'); ?>
            <div class="fvcn-post-form-thumbnail">
                <label for="fvcn_post_form_thumbnail"><?php fvcn_post_form_thumbnail_label(); ?></label>
                <input type="file" name="fvcn_post_form_thumbnail" id="fvcn_post_form_thumbnail" value="">
                <div class="fvcn-error">
                    <?php fvcn_post_form_field_error('fvcn_post_form_thumbnail'); ?>
                </div>
            </div>
            <?php do_action('fvcn_post_form_after_thumbnail'); ?>

        <?php endif; ?>

        <?php do_action('fvcn_post_form_before_submit'); ?>
        <br>
        <div class="fvcn-post-form-submit">
            <input type="submit" name="fvcn_post_form_submit" id="fvcn_post_form_submit" value="<?php _e('Submit', 'fvcn'); ?>">

            <span class="fvcn-post-form-loader">
                <svg class="fvcn-post-form-loader-img" aria-hidden="true" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path d="M440.935 12.574l3.966 82.766C399.416 41.904 331.674 8 256 8 134.813 8 33.933 94.924 12.296 209.824 10.908 217.193 16.604 224 24.103 224h49.084c5.57 0 10.377-3.842 11.676-9.259C103.407 137.408 172.931 80 256 80c60.893 0 114.512 30.856 146.104 77.801l-101.53-4.865c-6.845-.328-12.574 5.133-12.574 11.986v47.411c0 6.627 5.373 12 12 12h200.333c6.627 0 12-5.373 12-12V12c0-6.627-5.373-12-12-12h-47.411c-6.853 0-12.315 5.729-11.987 12.574zM256 432c-60.895 0-114.517-30.858-146.109-77.805l101.868 4.871c6.845.327 12.573-5.134 12.573-11.986v-47.412c0-6.627-5.373-12-12-12H12c-6.627 0-12 5.373-12 12V500c0 6.627 5.373 12 12 12h47.385c6.863 0 12.328-5.745 11.985-12.599l-4.129-82.575C112.725 470.166 180.405 504 256 504c121.187 0 222.067-86.924 243.704-201.824 1.388-7.369-4.308-14.176-11.807-14.176h-49.084c-5.57 0-10.377 3.842-11.676 9.259C408.593 374.592 339.069 432 256 432z"></path>
                </svg>
            </span>
        </div>
        <?php do_action('fvcn_post_form_after_submit'); ?>

        <?php fvcn_post_form_fields(); ?>

        <?php do_action('fvcn_post_form_extras_bottom'); ?>
    </form>
</div>

<?php do_action('fvcn_post_form_after'); ?>

<?php endif; ?>
