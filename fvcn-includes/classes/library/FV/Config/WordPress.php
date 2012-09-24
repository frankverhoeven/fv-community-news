<?php

/**
 * FV_Config_WordPress
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
class FV_Config_WordPress implements FV_Config_Interface
{
    /**
     * Config object cache.
     * @var array
     */
    protected $_config;

    /**
     * Default config.
     * @var FV_Config_Interface
     */
    protected $_defaultConfig;

    /**
     * Constructor.
     *
     * @param FV_Config_Interface $default
     */
    public function __construct(FV_Config_Interface $default)
    {
        $this->_defaultConfig = $default;
    }

    /**
     * Get an option.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default=null)
    {
        if (isset($this->_config[ $key ])) {
            return $this->_config[ $key ];
        }

        $default = (null === $default ? $this->getDefault($key) : $default);

        return $this->_config[ $key ] = get_option($key, $default);
    }

    /**
     * Get default option.
     *
     * @param string $key
     * @return mixed
     */
    public function getDefault($key)
    {
        return $this->_defaultConfig->get($key);
    }

    public function set($key, $value)
    {
        $this->_config[ $key ] = $value;
        update_option($key, $value);

        return $this;
    }
}
