<?php

namespace FvCommunityNews;

/**
 * Registry
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Registry
{
    /**
     * @var array
     */
    private $options = [];
    /**
     * @var Registry
     */
    private static $instance;

    /**
     * __construct()
     *
     * @version 20120710
     * @param array $options
     */
    public function __construct(array $options=null)
    {
        if (!empty($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * __set()
     *
     * @version 20120710
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->options[ $key ] = $value;
    }

    /**
     * __get()
     *
     * @version 20120710
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (!isset($this->options[ $key ])) {
            return null;
        }

        return $this->options[ $key ];
    }

    /**
     * setOptions()
     *
     * @version 20120710
     * @param array $options
     * @return Registry
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key=>$value) {
            $this->$key = $value;
        }

        return $this;
    }

    /**
     * setInstance()
     *
     * @version 20120710
     * @param Registry $instance
     */
    public static function setInstance(Registry $instance=null)
    {
        if (null === self::$instance) {
            if (null === $instance) {
                self::$instance = new Registry();
            } else {
                self::$instance = $instance;
            }
        }
    }

    /**
     * getInstance()
     *
     * @version 20120710
     * @return Registry
     */
    public static function getInstance()
    {
        self::setInstance();
        return self::$instance;
    }

    /**
     * set()
     *
     * @version 20120710
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        self::getInstance()->$key = $value;
    }

    /**
     * get()
     *
     * @version 20120710
     * @param string $key
     * @return mixed
     */
    public static function get($key)
    {
        return self::getInstance()->$key;
    }
}
