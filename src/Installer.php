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
     * @var Config
     */
    protected $config;

    /**
     * __construct()
     *
     * @param Config $config
     * @version 20171112
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * isInstall()
     *
     * @return bool
     * @version 20171112
     */
    public function isInstall()
    {
        return (false === $this->config->get('_fvcn_version', false));
    }

    /**
     * isUpdate()
     *
     * @return bool
     * @version 20171112
     */
    public function isUpdate()
    {
        return (1 == version_compare(Version::getCurrentVersion(), $this->config['_fvcn_version']));
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
        $this->config->set('_fvcn_version', Version::getCurrentVersion());

        return $this;
    }

    /**
     * Add options to the database
     *
     * @return Installer
     * @version 20171112
     */
    public function addOptions()
    {
        foreach ($this->config as $key => $value) {
            $this->config->add($key, $value);
        }

        return $this;
    }

    /**
     * Check if an update is available.
     *
     * @return bool
     * @version 20171112
     */
    public function hasUpdate()
    {
        $lastCheck = $this->config->get('_fvcn_previous_has_update', false);
        if (!$lastCheck || (time() - $lastCheck) > 432000) { // Only check once every five days
            $latest = Version::getLatestVersion();
            $this->config->set('_fvcn_previous_has_update', time());

            if (null !== $latest) {
                return (1 == version_compare($latest, $this->config['_fvcn_version']));
            }
        }

        return false;
    }
}
