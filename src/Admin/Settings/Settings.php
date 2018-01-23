<?php

namespace FvCommunityNews\Admin\Settings;

/**
 * Settings
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Settings extends AbstractSettings
{
    public function __construct()
    {
        add_settings_section('fvcn_settings_general', __('General Settings', 'fvcn'), [$this, 'general_section'], 'fvcn-settings');

        add_settings_field('_fvcn_moderation', __('Before a post appears&hellip;', 'fvcn'), [$this, 'moderation'], 'fvcn-settings', 'fvcn_settings_general');
        register_setting('fvcn-settings', '_fvcn_admin_moderation', 'boolval');
        register_setting('fvcn-settings', '_fvcn_user_moderation', 'boolval');

        add_settings_field('_fvcn_mail', __('Send a notification mail&hellip;', 'fvcn'), [$this, 'mail'], 'fvcn-settings', 'fvcn_settings_general');
        register_setting('fvcn-settings', '_fvcn_mail_on_submission', 'boolval');
        register_setting('fvcn-settings', '_fvcn_mail_on_moderation', 'boolval');

        add_settings_field('_fvcn_is_anonymous_allowed', __('Authentication', 'fvcn'), [$this, 'is_anonymous_allowed'], 'fvcn-settings', 'fvcn_settings_general');
        register_setting('fvcn-settings', '_fvcn_is_anonymous_allowed', 'boolval');


        add_settings_section('fvcn_settings_permalinks', __('Permalinks', 'fvcn'), [$this, 'permalinks_section'], 'fvcn-settings');

        add_settings_field('_fvcn_base_slug', __('Base Slug', 'fvcn'), [$this, 'post_base_slug'], 'fvcn-settings', 'fvcn_settings_permalinks');
        register_setting('fvcn-settings', '_fvcn_base_slug', 'esc_sql');

        add_settings_field('_fvcn_post_slug', __('Post Slug', 'fvcn'), [$this, 'post_slug'], 'fvcn-settings', 'fvcn_settings_permalinks');
        register_setting('fvcn-settings', '_fvcn_post_slug', 'esc_sql');

        add_settings_field('_fvcn_post_tag_slug', __('Tag Slug', 'fvcn'), [$this, 'post_tag_slug'], 'fvcn-settings', 'fvcn_settings_permalinks');
        register_setting('fvcn-settings', '_fvcn_post_tag_slug', 'esc_sql');

        add_settings_field('_fvcn_post_archive_slug', __('Archive Slug', 'fvcn'), [$this, 'post_archive_slug'], 'fvcn-settings', 'fvcn_settings_permalinks');
        register_setting('fvcn-settings', '_fvcn_post_archive_slug', 'esc_sql');


        do_action('fvcn_register_admin_settings');
    }

    public function fvcn_admin_settings()
    {
        flush_rewrite_rules();
        ?>
        <div class="wrap">
            <h1><?php _e('FV Community News Settings', 'fvcn'); ?></h1>
            <?php settings_errors(); ?>

            <form action="<?= admin_url('options.php'); ?>" method="post">
                <?php settings_fields('fvcn-settings'); ?>

                <?php do_settings_sections('fvcn-settings'); ?>

                <p class="submit">
                    <input type="submit" name="submit" class="button-primary" value="<?php _e('Save Changes', 'fvcn'); ?>">
                </p>
            </form>
        </div>
        <?php
    }

    public function general_section()
    {}

    public function moderation()
    {
        echo $this->checkboxField('_fvcn_admin_moderation', 'an administrator must always approve the post.');
        echo $this->checkboxField('_fvcn_user_moderation', 'the user must have a previously approved post (authentication required).');
    }

    public function mail()
    {
        echo $this->checkboxField('_fvcn_mail_on_submission', 'when a post is submitted.');
        echo $this->checkboxField('_fvcn_mail_on_moderation', 'when a post is held for moderation.');
    }

    public function is_anonymous_allowed()
    {
        echo $this->checkboxField('_fvcn_is_anonymous_allowed', 'Anonymous allowed (disable to require authentication).');
    }

    public function permalinks_section()
    {
        printf(
            __('<p>Here you can set the <a href="%s">permalink</a> structure bases.</p>', 'fvcn'),
            admin_url('options-permalink.php')
        );
    }

    public function post_base_slug()
    {
        echo $this->inputField('_fvcn_base_slug');
    }

    public function post_slug()
    {
        echo $this->inputField('_fvcn_post_slug');
    }

    public function post_tag_slug()
    {
        echo $this->inputField('_fvcn_post_tag_slug');
    }

    public function post_archive_slug()
    {
        echo $this->inputField('_fvcn_post_archive_slug');
    }
}
