<?php

/**
 * FV_Interface
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
interface FV_Config_Interface
{
    /**
     * Get an option.
     *
     * @param string $key
     */
    public function get($key);

    /**
     * Set an option.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value);
}
