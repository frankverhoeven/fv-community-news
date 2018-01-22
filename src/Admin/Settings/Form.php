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
     * @var bool
     */
    private $advanced = false;
    
    public function __construct()
    {
        $this->advanced = false;
        if (isset($_REQUEST['mode']) && 'advanced' == $_REQUEST['mode']) {
            $this->advanced = true;
        }

        // Author Name
        add_settings_section('fvcn_form_author_name', __('Author Name', 'fvcn'), [$this, 'author_name_section'], 'fvcn-form');

        add_settings_field('_fvcn_post_form_author_name_label', __('Label', 'fvcn'), [$this, 'author_name_label'], 'fvcn-form', 'fvcn_form_author_name');
        register_setting('fvcn-form', '_fvcn_post_form_author_name_label', 'esc_sql');

        if ($this->advanced) {
            add_settings_field('_fvcn_post_form_author_name_length_min', __('Minimum Length', 'fvcn'), [$this, 'author_name_length_min'], 'fvcn-form', 'fvcn_form_author_name');
            register_setting('fvcn-form', '_fvcn_post_form_author_name_length_min', 'intval');
            add_settings_field('_fvcn_post_form_author_name_length_max', __('Maximum Length', 'fvcn'), [$this, 'author_name_length_max'], 'fvcn-form', 'fvcn_form_author_name');
            register_setting('fvcn-form', '_fvcn_post_form_author_name_length_max', 'intval');
        }


        // Author Email
        add_settings_section('fvcn_form_author_email', __('Author Email', 'fvcn'), [$this, 'author_email_section'], 'fvcn-form');

        add_settings_field('_fvcn_post_form_author_email_label', __('Label', 'fvcn'), [$this, 'author_email_label'], 'fvcn-form', 'fvcn_form_author_email');
        register_setting('fvcn-form', '_fvcn_post_form_author_email_label', 'esc_sql');


        // Title
        add_settings_section('fvcn_form_title', __('Title', 'fvcn'), [$this, 'title_section'], 'fvcn-form');

        add_settings_field('_fvcn_post_form_title_label', __('Label', 'fvcn'), [$this, 'title_label'], 'fvcn-form', 'fvcn_form_title');
        register_setting('fvcn-form', '_fvcn_post_form_title_label', 'esc_sql');

        if ($this->advanced) {
            add_settings_field('_fvcn_post_form_title_length_min', __('Minimum Length', 'fvcn'), [$this, 'title_length_min'], 'fvcn-form', 'fvcn_form_title');
            register_setting('fvcn-form', '_fvcn_post_form_title_length_min', 'intval');
            add_settings_field('_fvcn_post_form_title_length_max', __('Maximum Length', 'fvcn'), [$this, 'title_length_max'], 'fvcn-form', 'fvcn_form_title');
            register_setting('fvcn-form', '_fvcn_post_form_title_length_max', 'intval');
        }


        // Link
        add_settings_section('fvcn_form_link', __('Link', 'fvcn'), [$this, 'link_section'], 'fvcn-form');

        add_settings_field('_fvcn_post_form_link_label', __('Label', 'fvcn'), [$this, 'link_label'], 'fvcn-form', 'fvcn_form_link');
        register_setting('fvcn-form', '_fvcn_post_form_link_label', 'esc_sql');

        add_settings_field('_fvcn_post_form_link_required', __('Required', 'fvcn'), [$this, 'link_required'], 'fvcn-form', 'fvcn_form_link');
        register_setting('fvcn-form', '_fvcn_post_form_link_required', 'intval');

        if ($this->advanced) {
            add_settings_field('_fvcn_post_form_link_length_min', __('Minimum Length', 'fvcn'), [$this, 'link_length_min'], 'fvcn-form', 'fvcn_form_link');
            register_setting('fvcn-form', '_fvcn_post_form_link_length_min', 'intval');
            add_settings_field('_fvcn_post_form_link_length_max', __('Maximum Length', 'fvcn'), [$this, 'link_length_max'], 'fvcn-form', 'fvcn_form_link');
            register_setting('fvcn-form', '_fvcn_post_form_link_length_max', 'intval');
        }


        // Content
        add_settings_section('fvcn_form_content', __('Content', 'fvcn'), [$this, 'content_section'], 'fvcn-form');

        add_settings_field('_fvcn_post_form_content_label', __('Label', 'fvcn'), [$this, 'content_label'], 'fvcn-form', 'fvcn_form_content');
        register_setting('fvcn-form', '_fvcn_post_form_content_label', 'esc_sql');

        if ($this->advanced) {
            add_settings_field('_fvcn_post_form_content_length_min', __('Minimum Length', 'fvcn'), [$this, 'content_length_min'], 'fvcn-form', 'fvcn_form_content');
            register_setting('fvcn-form', '_fvcn_post_form_content_length_min', 'intval');
            add_settings_field('_fvcn_post_form_content_length_max', __('Maximum Length', 'fvcn'), [$this, 'content_length_max'], 'fvcn-form', 'fvcn_form_content');
            register_setting('fvcn-form', '_fvcn_post_form_content_length_max', 'intval');
        }


        // Tags
        add_settings_section('fvcn_form_tags', __('Tags', 'fvcn'), [$this, 'tags_section'], 'fvcn-form');

        add_settings_field('_fvcn_post_form_tags_label', __('Label', 'fvcn'), [$this, 'tags_label'], 'fvcn-form', 'fvcn_form_tags');
        register_setting('fvcn-form', '_fvcn_post_form_tags_label', 'esc_sql');

        add_settings_field('_fvcn_post_form_tags_required', __('Required', 'fvcn'), [$this, 'tags_required'], 'fvcn-form', 'fvcn_form_tags');
        register_setting('fvcn-form', '_fvcn_post_form_tags_required', 'intval');

        if ($this->advanced) {
            add_settings_field('_fvcn_post_form_tags_length_min', __('Minimum Length', 'fvcn'), [$this, 'tags_length_min'], 'fvcn-form', 'fvcn_form_tags');
            register_setting('fvcn-form', '_fvcn_post_form_tags_length_min', 'intval');
            add_settings_field('_fvcn_post_form_tags_length_max', __('Maximum Length', 'fvcn'), [$this, 'tags_length_max'], 'fvcn-form', 'fvcn_form_tags');
            register_setting('fvcn-form', '_fvcn_post_form_tags_length_max', 'intval');
        }


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

    public function fvcn_admin_form()
    {
        ?>
        <div class="wrap">
            <h1><?php _e('FV Community News Form', 'fvcn'); ?></h1>
            <?php settings_errors(); ?>

            <h2 class="nav-tab-wrapper wp-clearfix">
                <a href="<?= esc_url(add_query_arg(['page' => 'fvcn-form', 'post_type' => 'fvcn-post'], admin_url('edit.php'))); ?>" class="nav-tab<?php if (!$this->advanced) echo ' nav-tab-active'; ?>"><?php esc_html_e('Basic', 'fvcn'); ?></a>
                <a href="<?= esc_url(add_query_arg(['page' => 'fvcn-form', 'post_type' => 'fvcn-post', 'mode' => 'advanced'], admin_url('edit.php'))); ?>" class="nav-tab<?php if ($this->advanced) echo ' nav-tab-active'; ?>"><?php esc_html_e('Advanced', 'fvcn'); ?></a>
            </h2>

            <form action="<?= admin_url('options.php'); ?>" method="post">
                <?php settings_fields('fvcn-form'); ?>

                <?php do_settings_sections('fvcn-form'); ?>

                <?php if ($this->advanced) echo '<input type="hidden" name="mode" value="advanced">'; ?>
                <p class="submit">
                    <input type="submit" name="submit" class="button-primary" value="<?php _e('Save Changes', 'fvcn'); ?>">
                </p>
            </form>
        </div>
        <?php
    }

    public function fvcn_admin_form_help()
    {
        $screen = get_current_screen();

        $content = '<p>' . __('This screen provides access to the basic form configuration.', 'fvcn') . '</p>';
        $content .= '<ul><li>' . __('Change the label of a form field, this is the value displayed above the field. Note that it is <em>not</em> possible to use any html.', 'fvcn') . '</li>';
        $content .= '<li>' . __('Make a field required or optional.', 'fvcn') . '</li></ul>';
        $content .= '<p>' . __('Remember to save your settings when you are done!', 'fvcn') . '</p>';

        $screen->add_help_tab([
            'id' => 'fvcn-admin-form-help-basic',
            'title' => __('Basic', 'fvcn'),
            'content' => $content
        ]);

        $content = '<p>' . __('This screen provides access to the advanced form configuration.', 'fvcn') . '</p>';
        $content .= '<p>' . __('It allows you to set the minimum/maximum number of characters a field requires.', 'fvcn') . '</p>';

        $screen->add_help_tab([
            'id' => 'fvcn-admin-form-help-advanced',
            'title' => __('Advanced', 'fvcn'),
            'content' => $content
        ]);
    }

    public function author_name_section()
    {
        echo $this->sectionDescription('Author name field settings.');
    }

    public function author_name_label()
    {
        echo $this->inputField('_fvcn_post_form_author_name_label');
    }

    public function author_name_length_min()
    {
        echo $this->inputField('_fvcn_post_form_author_name_length_min', 'number');
    }

    public function author_name_length_max()
    {
        echo $this->inputField('_fvcn_post_form_author_name_length_max', 'number');
    }

    public function author_email_section()
    {
        echo $this->sectionDescription('Author email field settings.');
    }

    public function author_email_label()
    {
        echo $this->inputField('_fvcn_post_form_author_email_label');
    }

    public function title_section()
    {
        echo $this->sectionDescription('Title field settings.');
    }

    public function title_label()
    {
        echo $this->inputField('_fvcn_post_form_title_label');
    }

    public function title_length_min()
    {
        echo $this->inputField('_fvcn_post_form_title_length_min', 'number');
    }

    public function title_length_max()
    {
        echo $this->inputField('_fvcn_post_form_title_length_max', 'number');
    }

    public function link_section()
    {
        echo $this->sectionDescription('Link field settings.');
    }

    public function link_label()
    {
        echo $this->inputField('_fvcn_post_form_link_label');
    }

    public function link_required()
    {
        echo $this->checkboxField('_fvcn_post_form_link_required', 'Make the link field a required field.');
    }

    public function link_length_min()
    {
        echo $this->inputField('_fvcn_post_form_link_length_min', 'number');
    }

    public function link_length_max()
    {
        echo $this->inputField('_fvcn_post_form_link_length_max', 'number');
    }

    public function content_section()
    {
        echo $this->sectionDescription('Content field settings.');
    }

    public function content_label()
    {
        echo $this->inputField('_fvcn_post_form_content_label');
    }

    public function content_length_min()
    {
        echo $this->inputField('_fvcn_post_form_content_length_min', 'number');
    }

    public function content_length_max()
    {
        echo $this->inputField('_fvcn_post_form_content_length_max', 'number');
    }

    public function tags_section()
    {
        echo $this->sectionDescription('Tags field settings.');
    }

    public function tags_label()
    {
        echo $this->inputField('_fvcn_post_form_tags_label');
    }

    public function tags_required()
    {
        echo $this->checkboxField('_fvcn_post_form_tags_required', 'Make the tags field a required field.');
    }

    public function tags_length_min()
    {
        echo $this->inputField('_fvcn_post_form_tags_length_min', 'number');
    }

    public function tags_length_max()
    {
        echo $this->inputField('_fvcn_post_form_tags_length_max', 'number');
    }

    public function thumbnail_section()
    {
        echo $this->sectionDescription('Thumbnail field settings.');
    }

    public function thumbnail_enabled()
    {
        echo $this->checkboxField('_fvcn_post_form_thumbnail_enabled', 'Enable the thumbnail field.');
    }

    public function thumbnail_label()
    {
        echo $this->inputField('_fvcn_post_form_thumbnail_label');
    }

    public function thumbnail_required()
    {
        echo $this->checkboxField('_fvcn_post_form_thumbnail_required', 'Make the thumbnail field a required field.');
    }

    /**
     * Generate section description.
     *
     * @param string $description
     * @return string
     */
    protected function sectionDescription(string $description): string
    {
        return sprintf('<p>%s</p>', __($description, 'fvcn'));
    }

    /**
     * Generate input field.
     *
     * @param string $id
     * @param string $type
     * @return string
     */
    protected function inputField(string $id, string $type = 'text'): string
    {
        return sprintf('<input type="%s" name="%s" id="%s" value="%s" class="reqular-text">',
            $type, $id, $id, fvcn_get_form_option($id)
        );
    }

    protected function checkboxField(string $id, string $label): string
    {
        return sprintf('<input type="checkbox" name="%s" id="%s" value="1" %s><label for="%s">%s</label>',
            $id, $id, checked((bool) get_option($id), true, false), $id, __($label, 'fvcn')
        );
    }
}
