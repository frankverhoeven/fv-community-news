<?php

namespace FvCommunityNews\Admin\Settings;

/**
 * AbstractSettings
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
abstract class AbstractSettings
{
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
        return sprintf('<input type="%s" name="%s" id="%s" value="%s" class="regular-text">',
            $type, $id, $id, esc_attr(fvcn_get_form_option($id))
        );
    }

    /**
     * Generate checkbox field.
     *
     * @param string $id
     * @param string $label
     * @return string
     */
    protected function checkboxField(string $id, string $label): string
    {
        return sprintf('<label for="%s"><input type="checkbox" name="%s" id="%s" value="1" %s>%s</label><br>',
            $id, $id, $id, checked((bool) fvcn_get_form_option($id), true, false), __($label, 'fvcn')
        );
    }
}
