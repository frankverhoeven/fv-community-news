<?php

/**
 * FV_Config
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class FV_Config
{
    /**
     * Configuration.
     * @var FV_Config_Interface
     */
    protected $_config;

    /**
     * Constructor.
     *
     * @param FV_Config_Interface $config
     * @return void
     */
    public function __construct(FV_Config_Interface $config)
    {
        $this->_config = $config;
    }

    public function get($key)
    {

    }

    public function set($key, $value)
    {

    }

    public function add($key, $value)
    {

    }

    public function remove($key)
    {

    }
}
