<?php

namespace FvCommunityNews\Config;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * AbstractConfig
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
abstract class AbstractConfig implements ArrayAccess, Countable, Iterator
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @param array $config
     */
    public function __construct(array $config = null)
    {
        if (null !== $config) {
            foreach ($config as $key => $value) {
                if (is_array($value)) {
                    $this->config[$key] = new static($value);
                } else {
                    $this->config[$key] = $value;
                }
            }
        }
    }

    /**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    abstract public function get(string $key, $default = null);

    /**
     * Add a value to the config, skips if key exists.
     *
     * @param  string $key
     * @param  mixed  $value
     */
    abstract public function add(string $key, $value);

    /**
     * Set a value in the config.
     *
     * @param string $key
     * @param mixed $value
     */
    abstract public function set(string $key, $value);

    /**
     * Whether an option exists.
     *
     * @param string $key
     * @return bool
     */
    abstract public function has(string $key): bool;

    /**
     * Delete an option
     *
     * @param string $key
     */
    abstract public function delete(string $key);

    /**
     * Whether a offset exists
     *
     * @param mixed $offset An offset to check for.
     * @return boolean true on success or false on failure.
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
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset The offset to unset.
     */
    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        return current($this->config);
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->config);
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->config);
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean Returns true on success or false on failure.
     */
    public function valid()
    {
        return ($this->key() !== null);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->config);
    }

    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer.
     */
    public function count()
    {
        return count($this->config);
    }
}
