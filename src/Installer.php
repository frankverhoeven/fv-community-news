<?php

namespace FvCommunityNews;

use FvCommunityNews\Config\AbstractConfig as Config;

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
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * isInstall()
     *
     * @return bool
     */
    public function isInstall()
    {
        return (false === $this->config->get('_fvcn_version', false));
    }

    /**
     * isUpdate()
     *
     * @return bool
     */
    public function isUpdate()
    {
        return (1 == version_compare(Version::getCurrentVersion(), $this->config['_fvcn_version']));
    }

    /**
     * install()
     *
     * @return Installer
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
     */
    public function hasUpdate()
    {
        $lastCheck = $this->config->get('_fvcn_previous_has_update', false);
        if (!$lastCheck || (time() - $lastCheck) > 86400) { // Only check once every 24 hours
            $latest = Version::getLatestVersion();
            $this->config->set('_fvcn_previous_has_update', time());

            if (null !== $latest) {
                return (1 == version_compare($latest, $this->config['_fvcn_version']));
            }
        }

        return false;
    }
}
