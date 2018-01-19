<?php

namespace FvCommunityNews\Config;

/**
 * Memory
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Memory extends AbstractConfig
{
    /**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->config)) {
            $default = $this->config[$key];
        }

        return $default;
    }

    /**
     * Add a value to the config, skips if key exists.
     *
     * @param  string $key
     * @param  mixed $value
     */
    public function add(string $key, $value)
    {
        if (is_array($value)) {
            $value = new static($value);
        }
        if (!array_key_exists($key, $this->config)) {
            $this->config[$key] = $value;
        }
    }

    /**
     * Set a value in the config.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value)
    {
        if (is_array($value)) {
            $value = new static($value);
        }

        $this->config[$key] = $value;
    }

    /**
     * Whether an option exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->config[$key]);
    }

    /**
     * Delete an option
     *
     * @param string $key
     */
    public function delete(string $key): void
    {
        if (isset($this->config[$key])) {
            unset($this->config[$key]);
        }
    }
}
