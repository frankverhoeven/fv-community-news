<?php

namespace FvCommunityNews;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * Config
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Config implements Countable, Iterator, ArrayAccess
{
    /**
     * @var array
     */
    private $config;

    /**
     * __construct()
     *
     * @param array $config
     * @version 20171112
     */
    public function __construct(array $config)
    {
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                $this->config[$key] = new static($value);
            } else {
                $this->config[$key] = $value;
            }
        }
    }

    /**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     * @version 20171112
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->config)) {
            $default = $this->config[$key];
        }

        return get_option($key, $default);
    }

    /**
     * Add a value to the config, skips if key exists.
     *
     * @param  string $key
     * @param  mixed  $value
     * @version 20171112
     */
    public function add($key, $value)
    {
        if (is_array($value)) {
            $value = new static($value);
        }

        if (!array_key_exists($key, $this->config)) {
            $this->config[$key] = $value;
        }
        add_option($key, $value);
    }

    /**
     * Set a value in the config.
     *
     * @param string $key
     * @param mixed $value
     * @version 20171112
     */
    public function set($key, $value)
    {
        if (is_array($value)) {
            $value = new static($value);
        }

        $this->config[$key] = $value;
        update_option($key, $value);
    }

    /**
     * Whether an option exists.
     *
     * @param string $key
     * @return bool
     * @version 20171112
     */
    public function has($key)
    {
        return isset($this->config[$key]);
    }

    /**
     * Delete an option
     *
     * @param string $key
     * @version 20171112
     */
    public function delete($key)
    {
        if (isset($this->config[$key])) {
            unset($this->config[$key]);
            delete_option($key);
        }
    }

    /**
     * Whether a offset exists
     *
     * @param mixed $offset An offset to check for.
     * @return boolean true on success or false on failure.
     * @version 20171112
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset The offset to retrieve.
     * @return mixed Can return all value types.
     * @version 20171112
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value The value to set.
     * @version 20171112
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset The offset to unset.
     * @version 20171112
     */
    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     * @version 20171112
     */
    public function current()
    {
        return current($this->config);
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     * @version 20171112
     */
    public function next()
    {
        next($this->config);
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     * @version 20171112
     */
    public function key()
    {
        return key($this->config);
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean Returns true on success or false on failure.
     * @version 20171112
     */
    public function valid()
    {
        return ($this->key() !== null);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     * @version 20171112
     */
    public function rewind()
    {
        reset($this->config);
    }

    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer.
     * @version 20171112
     */
    public function count()
    {
        return count($this->config);
    }
}
