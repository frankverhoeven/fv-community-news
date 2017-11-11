<?php

namespace FvCommunityNews;

/**
 * Installer
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Installer
{
    /**
     * @var Options
     */
    protected $options;

    /**
     * __construct()
     *
     * @param Options $options
     * @version 20171111
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    /**
     * isInstall()
     *
     * @version 20120716
     * @return bool
     */
    public function isInstall()
    {
        return (false === $this->options->getOption('_fvcn_version', false));
    }

    /**
     * isUpdate()
     *
     * @version 20120716
     * @return bool
     */
    public function isUpdate()
    {
        return (1 == version_compare($this->options->getDefaultOption('_fvcn_version'), $this->options->getOption('_fvcn_version')));
    }

    /**
     * install()
     *
     * @return Installer
     * @version 20171111
     */
    public function install()
    {
        $this->addOptions();

        return $this;
    }

    /**
     * update()
     *
     * @return Installer
     * @version 20171111
     */
    public function update()
    {
        $this->addOptions();

        $this->options->updateOption('_fvcn_version', $this->options->getDefaultOption('_fvcn_version'));

        return $this;
    }

    /**
     * addOptions()
     *
     * @version 20120716
     * @return Installer
     */
    public function addOptions()
    {
        $this->options->addOptions();
        return $this;
    }

    /**
     * Check if an update is available.
     *
     * @return bool
     * @version 20171111
     */
    public function hasUpdate()
    {
        $lastCheck = $this->options->getOption('_fvcn_previous_has_update', false);
        if (!$lastCheck || (time() - $lastCheck) > 432000) { // Only check once every five days
            $latest = Version::getLatestVersion();
            $this->options->updateOption('_fvcn_previous_has_update', time());

            if (null !== $latest) {
                return (1 == version_compare($latest, $this->options->getOption('_fvcn_version')));
            }
        }

        return false;
    }
}
