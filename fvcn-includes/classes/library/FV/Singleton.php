<?php

/**
 * FV_Singleton
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
abstract class FV_Singleton
{
    /**
     * Class instance.
     * @var object
     */
    private static $_instance;

    /**
     * Set class instance. Create a new instance if none provided.
     *
     * @param object $instance
     * @throws Exception
     * @return void
     */
    public static function setInstance($instance=null)
    {
        if (null === self::$_instance) {
            if (null === $instance) {
                if (function_exists('get_called_class')) { // Pre 5.3
                    $class = get_called_class();
                } else {
                    $class = get_parent_class();

                    if (false === $class) {
                        throw new Exception('Invallid singleton instantiation');
                    }
                }
                
                self::$_instance = new $class();
            } else {
                if (!is_object($instance)) {
                    throw new Exception('$instance param must be an object');
                }

                self::$_instance = $instance;
            }
        }
    }

    /**
     * Get class instance. Create a new instance if none exists yet.
     *
     * @return object
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::setInstance();
        }

        return self::$_instance;
    }
}
