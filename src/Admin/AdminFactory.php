<?php

namespace FvCommunityNews\Admin;

use FvCommunityNews\Admin\Dashboard\Dashboard;
use FvCommunityNews\Admin\Post\Edit;
use FvCommunityNews\Admin\Post\Moderation;
use FvCommunityNews\Admin\Settings\Form;
use FvCommunityNews\Admin\Settings\Settings;

/**
 * AdminFactory
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class AdminFactory
{
    /**
     * @var Form
     */
    private $form;
    /**
     * @var Settings
     */
    private $settings;

    /**
     * getDashboard()
     *
     * @return Dashboard
     * @version 20171111
     */
    public function getDashboard()
    {
        return new Dashboard();
    }

    /**
     * getPostModeration()
     *
     * @return Moderation
     * @version 20171111
     */
    public function getPostModeration()
    {
        return new Moderation();
    }

    /**
     * getPostEdit()
     *
     * @return Edit
     * @version 20171111
     */
    public function getPostEdit()
    {
        return new Edit();
    }

    /**
     * getForm()
     *
     * @return Form
     * @version 20171111
     */
    public function getForm()
    {
        if (null === $this->form) {
            $this->form = new Form();
        }

        return $this->form;
    }

    /**
     * getSettings()
     *
     * @return Settings
     * @version 20171111
     */
    public function getSettings()
    {
        if (null === $this->settings) {
            $this->settings = new Settings();
        }

        return $this->settings;
    }
}
