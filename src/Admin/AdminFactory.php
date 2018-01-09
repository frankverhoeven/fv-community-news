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
     * @var Edit
     */
    private $edit;
    /**
     * @var Dashboard
     */
    private $dashboard;
    /**
     * @var Moderation
     */
    private $moderation;

    /**
     * getDashboard()
     *
     * @return Dashboard
     * @version 20171123
     */
    public function getDashboard()
    {
        if (null === $this->dashboard) {
            $this->dashboard = new Dashboard();
        }

        return $this->dashboard;
    }

    /**
     * getPostModeration()
     *
     * @return Moderation
     * @version 20171123
     */
    public function getPostModeration()
    {
        if (null === $this->moderation) {
            $this->moderation = new Moderation();
        }

        return $this->moderation;
    }

    /**
     * getPostEdit()
     *
     * @return Edit
     * @version 20171123
     */
    public function getPostEdit()
    {
        if (null === $this->edit) {
            $this->edit = new Edit();
        }

        return $this->edit;
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