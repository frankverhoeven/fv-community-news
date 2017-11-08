<?php

/**
 * FV_Registry
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class FV_Registry extends FV_Singleton
{
    /**
     * Registry data.
     * @var array
     */
    private $_registry;

    /**
     * Constructor. Set registry data.
     *
     * @param array $options
     * @return void
     */
    public function __construct(array $options=null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Set registry data.
     *
     * @param array $options
     * @return \FV_Registry
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key=>$value) {
            $this->$key = $value;
        }

        return $this;
    }

    /**
     * Set a registry item.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->_registry[ (string) $key ] = $value;
    }

    /**
     * Get a registry item.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->_registry[ (string) $key ];
    }

    /**
     * Check if a registry item exists.
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset( $this->_registry[ (string) $key ]);
    }

    /**
     * Remove a registry item.
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        unset( $this->_registry[ (string) $key ]);
    }

    /**
     * Set a registry item.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value)
    {
        self::getInstance()->$key = $value;
    }

    /**
     * Get a registry item.
     *
     * @param string $key
     * @return mixed
     */
    public static function get($key)
    {
        return self::getInstance()->$key;
    }
}
