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

        <?php do_action('fvcn_post_form_before_link'); ?>
        <div class="fvcn-post-form-link">
            <label for="fvcn_post_form_link"><?php fvcn_post_form_link_label(); ?></label>
            <input type="text" name="fvcn_post_form_link" id="fvcn_post_form_link" value="<?php fvcn_post_form_link(); ?>">
            <div class="fvcn-error">
                <?php fvcn_post_form_field_error('fvcn_post_form_link'); ?>
            </div>
        </div>
        <?php do_action('fvcn_post_form_after_link'); ?>

        <?php do_action('fvcn_post_form_before_content'); ?>
        <div class="fvcn-post-form-content">
            <label for="fvcn_post_form_content"><?php fvcn_post_form_content_label(); ?></label>
            <textarea name="fvcn_post_form_content" id="fvcn_post_form_content" rows="3"><?php fvcn_post_form_content(); ?></textarea>
            <div class="fvcn-error">
                <?php fvcn_post_form_field_error('fvcn_post_form_content'); ?>
            </div>
        </div>
        <?php do_action('fvcn_post_form_after_content'); ?>

        <?php do_action('fvcn_post_form_before_tags'); ?>
        <div class="fvcn-post-form-tags">
            <label for="fvcn_post_form_tags"><?php fvcn_post_form_tags_label(); ?></label>
            <input type="text" name="fvcn_post_form_tags" id="fvcn_post_form_tags" value="<?php fvcn_post_form_tags(); ?>">
            <div class="fvcn-error">
                <?php fvcn_post_form_field_error('fvcn_post_form_tags'); ?>
            </div>
        </div>
        <?php do_action('fvcn_post_form_after_tags'); ?>

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
        </div>
        <?php do_action('fvcn_post_form_after_submit'); ?>

        <?php fvcn_post_form_fields(); ?>

        <?php do_action('fvcn_post_form_extras_bottom'); ?>
    </form>
</div>

<?php do_action('fvcn_post_form_after'); ?>

<?php endif; ?>
