<?php

namespace FvCommunityNews\Admin\Settings;

/**
 * Form
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Form
{
    /**
     * __construct()
     *
     */
    public function __construct()
    {
        // Author Name
        add_settings_section('fvcn_form_author_name', __('Author Name', 'fvcn'), [$this, 'author_name_section'], 'fvcn-form');

        add_settings_field('_fvcn_post_form_author_name_label', __('Label', 'fvcn'), [$this, 'author_name_label'], 'fvcn-form', 'fvcn_form_author_name');
        register_setting('fvcn-form', '_fvcn_post_form_author_name_label', 'esc_sql');


        // Author Email
        add_settings_section('fvcn_form_author_email', __('Author Email', 'fvcn'), [$this, 'author_email_section'], 'fvcn-form');

        add_settings_field('_fvcn_post_form_author_email_label', __('Label', 'fvcn'), [$this, 'author_email_label'], 'fvcn-form', 'fvcn_form_author_email');
        register_setting('fvcn-form', '_fvcn_post_form_author_email_label', 'esc_sql');


        // Title
        add_settings_section('fvcn_form_title', __('Title', 'fvcn'), [$this, 'title_section'], 'fvcn-form');

        add_settings_field('_fvcn_post_form_title_label', __('Label', 'fvcn'), [$this, 'title_label'], 'fvcn-form', 'fvcn_form_title');
        register_setting('fvcn-form', '_fvcn_post_form_title_label', 'esc_sql');


        // Link
        add_settings_section('fvcn_form_link', __('Link', 'fvcn'), [$this, 'link_section'], 'fvcn-form');

        add_settings_field('_fvcn_post_form_link_label', __('Label', 'fvcn'), [$this, 'link_label'], 'fvcn-form', 'fvcn_form_link');
        register_setting('fvcn-form', '_fvcn_post_form_link_label', 'esc_sql');

        add_settings_field('_fvcn_post_form_link_required', __('Required', 'fvcn'), [$this, 'link_required'], 'fvcn-form', 'fvcn_form_link');
        register_setting('fvcn-form', '_fvcn_post_form_link_required', 'intval');


        // Content
        add_settings_section('fvcn_form_content', __('Content', 'fvcn'), [$this, 'content_section'], 'fvcn-form');

        add_settings_field('_fvcn_post_form_content_label', __('Label', 'fvcn'), [$this, 'content_label'], 'fvcn-form', 'fvcn_form_content');
        register_setting('fvcn-form', '_fvcn_post_form_content_label', 'esc_sql');


        // Tags
        add_settings_section('fvcn_form_tags', __('Tags', 'fvcn'), [$this, 'tags_section'], 'fvcn-form');

        add_settings_field('_fvcn_post_form_tags_label', __('Label', 'fvcn'), [$this, 'tags_label'], 'fvcn-form', 'fvcn_form_tags');
        register_setting('fvcn-form', '_fvcn_post_form_tags_label', 'esc_sql');

        add_settings_field('_fvcn_post_form_tags_required', __('Required', 'fvcn'), [$this, 'tags_required'], 'fvcn-form', 'fvcn_form_tags');
        register_setting('fvcn-form', '_fvcn_post_form_tags_required', 'intval');


        // Thumbnail
        add_settings_section('fvcn_form_thumbnail', __('Thumbnail', 'fvcn'), [$this, 'thumbnail_section'], 'fvcn-form');

        add_settings_field('_fvcn_post_form_thumbnail_enabled', __('Enabled', 'fvcn'), [$this, 'thumbnail_enabled'], 'fvcn-form', 'fvcn_form_thumbnail');
        register_setting('fvcn-form', '_fvcn_post_form_thumbnail_enabled', 'intval');


        add_settings_field('_fvcn_post_form_thumbnail_label', __('Label', 'fvcn'), [$this, 'thumbnail_label'], 'fvcn-form', 'fvcn_form_thumbnail');
        register_setting('fvcn-form', '_fvcn_post_form_thumbnail_label', 'esc_sql');

        add_settings_field('_fvcn_post_form_thumbnail_required', __('Required', 'fvcn'), [$this, 'thumbnail_required'], 'fvcn-form', 'fvcn_form_thumbnail');
        register_setting('fvcn-form', '_fvcn_post_form_thumbnail_required', 'intval');


        do_action('fvcn_register_admin_form_settings');
    }


    /**
     * fvcn_admin_form()
     *
     */
    public function fvcn_admin_form()
    {
        ?>
        <div class="wrap">
            <h2><?php _e('FV Community News Form', 'fvcn'); ?></h2>
            <?php settings_errors(); ?>

            <form action="options.php" method="post">
                <?php settings_fields('fvcn-form'); ?>

                <?php do_settings_sections('fvcn-form'); ?>

                <p class="submit">
                    <input type="submit" name="submit" class="button-primary" value="<?php _e('Save Changes', 'fvcn'); ?>">
                </p>
            </form>
        </div>
        <?php
    }


    /**
     * fvcn_admin_form_help()
     *
     */
    public function fvcn_admin_form_help()
    {
        $screen = get_current_screen();

        // Pre 3.3
        if (!method_exists($screen, 'add_help_tab')) {
            return;
        }

        $content = '<p>' . __('This screen provides access to the form configuration.', 'fvcn') . '</p>';
        $content .= '<ul><li>' . __('Change the label of a form field, this is the value displayed above the field. Note that it is <em>not</em> possible to use any html.', 'fvcn') . '</li>';
        $content .= '<li>' . __('Make a field required or optional.', 'fvcn') . '</li></ul>';
        $content .= '<p>' . __('Remember to save your settings when you are done!', 'fvcn') . '</p>';

        $screen->add_help_tab([
            'id' => 'fvcn-admin-form-help',
            'title' => __('Overview', 'fvcn'),
            'content' => $content
        ]);
    }


    /**
     * author_section()
     *
     */
    public function author_name_section()
    {
        ?>

        <p><?php _e('Author name field settings.', 'fvcn'); ?></p>

        <?php
    }

    /**
     * author_label()
     *
     */
    public function author_name_label()
    {
        ?>

        <input type="text" name="_fvcn_post_form_author_name_label" id="_fvcn_post_form_author_name_label" value="<?= fvcn_get_form_option('_fvcn_post_form_author_name_label'); ?>" class="reqular-text">

        <?php
    }


    /**
     * author_section()
     *
     */
    public function author_email_section()
    {
        ?>

        <p><?php _e('Author email field settings.', 'fvcn'); ?></p>

        <?php
    }

    /**
     * author_label()
     *
     */
    public function author_email_label()
    {
        ?>

        <input type="text" name="_fvcn_post_form_author_email_label" id="_fvcn_post_form_author_email_label" value="<?= fvcn_get_form_option('_fvcn_post_form_author_email_label'); ?>" class="reqular-text">

        <?php
    }


    /**
     * title_section()
     *
     */
    public function title_section()
    {
        ?>

        <p><?php _e('Title field settings.', 'fvcn'); ?></p>

        <?php
    }

    /**
     * title_label()
     *
     */
    public function title_label()
    {
        ?>

        <input type="text" name="_fvcn_post_form_title_label" id="_fvcn_post_form_title_label" value="<?= fvcn_get_form_option('_fvcn_post_form_title_label'); ?>" class="reqular-text">

        <?php
    }


    /**
     * link_section()
     *
     */
    public function link_section()
    {
        ?>

        <p><?php _e('Link field settings.', 'fvcn'); ?></p>

        <?php
    }

    /**
     * link_label()
     *
     */
    public function link_label()
    {
        ?>

        <input type="text" name="_fvcn_post_form_link_label" id="_fvcn_post_form_link_label" value="<?= fvcn_get_form_option('_fvcn_post_form_link_label'); ?>" class="reqular-text">

        <?php
    }

    /**
     * link_required()
     *
     */
    public function link_required()
    {
        ?>

        <input type="checkbox" name="_fvcn_post_form_link_required" id="_fvcn_post_form_link_required" value="1" <?php checked(get_option('_fvcn_post_form_link_required', true)); ?>>
        <label for="_fvcn_post_form_link_required"><?php _e('Make the link field a required field.', 'fvcn'); ?></label>

        <?php
    }


    /**
     * content_section()
     *
     */
    public function content_section()
    {
        ?>

        <p><?php _e('Content field settings.', 'fvcn'); ?></p>

        <?php
    }

    /**
     * content_label()
     *
     */
    public function content_label()
    {
        ?>

        <input type="text" name="_fvcn_post_form_content_label" id="_fvcn_post_form_content_label" value="<?= fvcn_get_form_option('_fvcn_post_form_content_label'); ?>" class="reqular-text">

        <?php
    }


    /**
     * tags_section()
     *
     */
    public function tags_section()
    {
        ?>

        <p><?php _e('Tags field settings.', 'fvcn'); ?></p>

        <?php
    }

    /**
     * tags_label()
     *
     */
    public function tags_label()
    {
        ?>

        <input type="text" name="_fvcn_post_form_tags_label" id="_fvcn_post_form_tags_label" value="<?= fvcn_get_form_option('_fvcn_post_form_tags_label'); ?>" class="reqular-text">

        <?php
    }

    /**
     * tags_required()
     *
     */
    public function tags_required()
    {
        ?>

        <input type="checkbox" name="_fvcn_post_form_tags_required" id="_fvcn_post_form_tags_required" value="1" <?php checked(get_option('_fvcn_post_form_tags_required', true)); ?>>
        <label for="_fvcn_post_form_tags_required"><?php _e('Make the tags field a required field.', 'fvcn'); ?></label>

        <?php
    }


    /**
     * thumbnail_section()
     *
     */
    public function thumbnail_section()
    {
        ?>

        <p><?php _e('Thumbnail field settings.', 'fvcn'); ?></p>

        <?php
    }


    /**
     * thumbnail_enabled()
     *
     */
    public function thumbnail_enabled()
    {
        ?>

        <input type="checkbox" name="_fvcn_post_form_thumbnail_enabled" id="_fvcn_post_form_thumbnail_enabled" value="1" <?php checked(get_option('_fvcn_post_form_thumbnail_enabled', true)); ?>>
        <label for="_fvcn_post_form_thumbnail_enabled"><?php _e('Enable the thumbnail field.', 'fvcn'); ?></label>

        <?php
    }
    
    /**
     * thumbnail_label()
     *
     */
    public function thumbnail_label()
    {
        ?>

        <input type="text" name="_fvcn_post_form_thumbnail_label" id="_fvcn_post_form_thumbnail_label" value="<?= fvcn_get_form_option('_fvcn_post_form_thumbnail_label'); ?>" class="reqular-text">

        <?php
    }

    /**
     * thumbnail_required()
     *
     */
    public function thumbnail_required()
    {
        ?>

        <input type="checkbox" name="_fvcn_post_form_thumbnail_required" id="_fvcn_post_form_thumbnail_required" value="1" <?php checked(get_option('_fvcn_post_form_thumbnail_required', false)); ?>>
        <label for="_fvcn_post_form_thumbnail_required"><?php _e('Make the thumbnail field a required field.', 'fvcn'); ?></label>

        <?php
    }
}
