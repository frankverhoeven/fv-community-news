<?php

/**
 * FV_Interface
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
interface FV_Config_Interface
{
    /**
     * Get an option.
     *
     * @param string $key
     */
    public function get($key);
}
