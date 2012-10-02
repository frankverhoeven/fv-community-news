<?php

/**
 * FV_Bootstrap
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
abstract class FV_Bootstrap
{
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {}

    /**
     * Start the bootstrap.
     *
     * @return FV_Bootstrap
     */
    public function bootstrap()
    {
        $methods = get_class_methods($this);

        foreach ($methods as $method) {
            if (0 === strpos($method, '_init')) {
                $this->$method();
            }
        }

        return $this;
    }
}

