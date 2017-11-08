<?php

/**
 * FV_Loader_Autoloader_Interface
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
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
