<?php

/**
 * FV_Loader_AutoloaderInterface
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
interface FV_Loader_AutoloaderInterface
{
    /**
     * Autoload function.
     *
     * @param string $className
     * @return void
     */
    public function autoload($className);
}
