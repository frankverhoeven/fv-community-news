<?php

/**
 * FV_Loader_Autoloader_Interface
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
interface FV_Loader_Autoloader_Interface
{
    /**
     * Register a namespace.
     *
     * @param string $namespace
     * @param string $location
     * @return \FV_Loader_AutoLoader
     * @throws Exception
     */
    public function registerNamespace($namespace, $location);

    /**
     * Register the autoloader.
     *
     * @return \FV_Loader_AutoLoader
     */
    public function register();

    /**
     * Autoload function.
     *
     * @param string $className
     * @return bool
     */
    public function autoload($className);
}
