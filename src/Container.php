<?php

namespace FvCommunityNews;

use FvCommunityNews\Admin\Admin;
use FvCommunityNews\Admin\AdminFactory;
use FvCommunityNews\Akismet\Akismet;
use FvCommunityNews\Akismet\Handler;
use FvCommunityNews\Post\Shortcodes;
use FvCommunityNews\View\AjaxForm;
use WP_Error;

/**
 * Container
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Container
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $objects = [];

    /**
     * @var Container
     */
    private static $instance;

    /**
     * __construct()
     *
     * @version 20120709
     * @param array $options
     */
    public function __construct(array $options= [])
    {
        $this->options = $options;
    }

    /**
     * getAdmin()
     *
     * @version 20120710
     * @return Admin
     */
    public function getAdmin()
    {
        if (isset($this->objects['admin'])) {
            return $this->objects['admin'];
        }

        return $this->objects['admin'] = new Admin(new AdminFactory());
    }

    /**
     * getAkismet()
     *
     * @version 20120711
     * @return Akismet
     * @todo: remove Akismet dependency (akismet_get_key())
     */
    public function getAkismet()
    {
        if (isset($this->objects['akismet'])) {
            return $this->objects['akismet'];
        }

        return $this->objects['akismet'] = new Akismet(akismet_get_key(), get_option('home'));
    }

    /**
     * getAkismetHandler()
     *
     * @version 20120711
     * @return Handler
     */
    public function getAkismetHandler()
    {
        if (isset($this->objects['akismetHandler'])) {
            return $this->objects['akismetHandler'];
        }

        return $this->objects['akismetHandler'] = new Handler($this->getAkismet());
    }

    /**
     * getJavascript()
     *
     * @version 20120714
     * @return AjaxForm
     */
    public function getJavascript()
    {
        if (isset($this->objects['javascript'])) {
            return $this->objects['javascript'];
        }

        return $this->objects['javascript'] = new AjaxForm();
    }

    /**
     * getOptions()
     *
     * @return Options
     * @version 20171111
     */
    public function getOptions()
    {
        if (isset($this->objects['options'])) {
            return $this->objects['options'];
        }

        return $this->objects['options'] = new Options(include __DIR__ . '/../config/default.config.php');
    }

    /**
     * getShortcodes()
     *
     * @version 20120709
     * @return Shortcodes
     */
    public function getShortcodes()
    {
        if (isset($this->objects['shortcodes'])) {
            return $this->objects['shortcodes'];
        }

        return $this->objects['shortcodes'] = new Shortcodes();
    }

    /**
     * getWpError()
     *
     * @version 20120709
     * @return WP_Error
     */
    public function getWpError()
    {
        if (isset($this->objects['wperror'])) {
            return $this->objects['wperror'];
        }

        return $this->objects['wperror'] = new WP_Error();
    }


    /**
     * setInstance()
     *
     * @version 20120710
     * @param Container $instance
     */
    public static function setInstance(Container $instance=null)
    {
        if (null === self::$instance) {
            if (null === $instance) {
                self::$instance = new Container();
            } else {
                self::$instance = $instance;
            }
        }
    }

    /**
     * getInstance()
     *
     * @version 20120710
     * @return Container
     */
    public static function getInstance()
    {
        self::setInstance();
        return self::$instance;
    }
}
