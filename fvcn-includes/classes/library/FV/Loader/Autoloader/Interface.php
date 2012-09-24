<?php

/**
 * FV_Loader_Autoloader_Interface
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
interface FV_Loader_Autoloader_Interface
{
    /**
     * Autoload function.
     *
     * @param string $className
     * @return bool
     */
    public function autoload($className);
}
