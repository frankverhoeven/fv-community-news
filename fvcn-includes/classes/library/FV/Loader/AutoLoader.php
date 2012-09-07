<?php

/**
 * FV_Loader_AutoLoader
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
class FV_Loader_AutoLoader implements FV_Loader_AutoloaderInterface
{
    /**
     * Registered Namespaces.
     * @var array
     */
    protected $_namespaces = array(
        'FV' => '../'
    );

    /**
     * Constructor. Optional register namespaces.
     *
     * @param array $namespaces
     * @return void
     */
    public function __construct(array $namespaces=null)
    {
        if (null !== $namespaces) {
            $this->registerNamespaces($namespaces);
        }
    }

    /**
     * Register the autoloader.
     *
     * @return \FV_Loader_AutoLoader
     */
    public function register()
    {
        spl_autoload_register(array($this, 'autoload'));
        return $this;
    }

    /**
     * Register a namespace.
     *
     * @param string $namespace
     * @param string $location
     * @return \FV_Loader_AutoLoader
     * @throws Exception
     */
    public function registerNamespace($namespace, $location)
    {
        if (!is_dir($location)) {
            throw new Exception( sprintf('Directory "%s" for namespace "%s" does not exist', $location, $namespace) );
        }

        $this->_namespaces[ $namespace ] = $location;
        
        return $this;
    }

    /**
     * Register namespaces.
     *
     * @param array $namespaces
     * @return \FV_Loader_AutoLoader
     */
    public function registerNamespaces(array $namespaces)
    {
        foreach ($namespaces as $namespace=>$location) {
            $this->registerNamespace($namespace, $location);
        }

        return $this;
    }

    /**
     * Autoloader.
     *
     * @param string $className
     * @return void
     */
    public function autoload($className)
    {

    }
}
