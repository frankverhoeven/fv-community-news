<?php

require_once './Autoloader/Interface.php';


/**
 * FV_Loader_AutoLoader
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
class FV_Loader_AutoLoader implements FV_Loader_Autoloader_Interface
{
    /**
     * File loader.
     * @var FV_Loader_Interface
     */
    protected $_loader;

    /**
     * Registered namespaces.
     * @var array
     */
    protected $_namespaces = array(
        'FV' => '../'
    );

    /**
     * Constructor. Optional register namespaces.
     *
     * @param FV_Loader_Interface $loader
     * @param array $namespaces
     * @return void
     */
    public function __construct(FV_Loader_Interface $loader, array $namespaces=null)
    {
        $this->_loader = $loader;

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
            throw new Exception( 'Directory "' . $location . ' for namespace "' . $namespace . '" does not exist' );
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
        $file = $this->convertClassNameToFilename($className);

        try {
            $this->_loader->loadFile($file);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Convert a class name to the corresponding filename.
     *
     * @param string $className
     * @return string
     */
    public function convertClassNameToFilename($className)
    {
        $filename = '';

        foreach ($this->_namespaces as $namespace => $location) {
            if (0 === strpos($className, $namespace)) {
                $filename .= $location;
            }
        }

        $filename .= './' . str_replace(array('_', '\\'), '/', $className) . '.php';

        return $filename;
    }
}
