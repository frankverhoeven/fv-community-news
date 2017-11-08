<?php

/**
 * FV_Config_Ini
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
class FV_Config_Ini implements FV_Config_Interface
{
    /**
     * Configuration.
     * @var array
     */
    private $_config;

    /**
     * Constructor. Set config file.
     *
     * @param string $iniFile
     */
    public function __construct($iniFile)
    {
        $config = parse_ini_file($iniFile);

        if (false === $config) {
            throw new Exception('Config file could not be loaded.');
        }

        $this->_config = $config;
    }

    /**
     * Get an option.
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->_config[ $key ];
    }

    /**
     * Set an option.
     *
     * @param string $key
     * @param mixed $value
     * @return \FV_Config_Ini
     */
    public function set($key, $value)
    {
        $this->_config[ $key ] = $value;
        return $this;
    }
}
