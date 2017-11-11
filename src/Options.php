<?php

namespace FvCommunityNews;

/**
 * Options
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Options
{
    /**
     * @var array
     */
    private $options;
	/**
	 * @var array
	 */
	private $defaults;

    /**
     * __construct()
     *
     * @param array $defaults
     * @version 20171111
     */
	public function __construct(array $defaults)
	{
	    $this->options = [];
        $this->defaults = apply_filters('fvcn_set_default_options', $defaults);
    }

	/**
	 * getDefaultOptions()
	 *
	 * @return array
     * @version 20171103
	 */
	public function getDefaultOptions()
	{
		return $this->defaults;
	}

	/**
	 * getDefaultOption()
	 *
	 * @param string $key
	 * @return mixed
     * @version 20171103
	 */
	public function getDefaultOption($key)
	{
		if (!isset($this->defaults[ $key ])) {
			return null;
		}

		return $this->defaults[ $key ];
	}

	/**
	 * addOptions()
	 *
     * @return $this
     * @version 20171103
	 */
	public function addOptions()
	{
		foreach ($this->getDefaultOptions() as $key => $value) {
			$this->addOption($key, $value);
		}

		return $this;
	}

    /**
     * addOption()
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     * @version 20171103
     */
	public function addOption($key, $value)
	{
		add_option($key, $value);
		$this->options[ $key ] = $value;

		return $this;
	}

    /**
     * updateOption()
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     * @version 20171103
     */
	public function updateOption($key, $value)
	{
		update_option($key, $value);
		$this->options[ $key ] = $value;

		return $this;
	}

	/**
	 * getOption()
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
     * @version 20171103
	 */
	public function getOption($key, $default = null)
	{
		if (isset($this->options[ $key ])) {
			return $this->options[ $key ];
		}
		if (null === $default) {
			return $this->options[ $key ] = get_option($key, $this->getDefaultOption($key));
		}

		return $this->options[ $key ] = get_option($key, $default);
	}

	/**
	 * deleteOptions()
	 *
     * @return $this
     * @version 20171103
	 */
	public function deleteOptions()
	{
		foreach ($this->getDefaultOptions() as $key => $value) {
			$this->deleteOption($key);
		}

		return $this;
	}

    /**
     * deleteOption()
     *
     * @param string $key
     * @return $this
     * @version 20171103
     */
	public function deleteOption($key)
	{
		delete_option($key);
		unset($this->options[ $key ]);

		return $this;
	}


    /**
     * fvcnGetDefaultOptions()
     *
     * @version 20120710
     * @return array
     */
    public static function fvcnGetDefaultOptions()
    {
        return Container::getInstance()->getOptions()->getDefaultOptions();
    }

    /**
     * fvcnGetDefaultOption()
     *
     * @version 20120710
     * @param string $key
     * @return mixed
     */
    public static function fvcnGetDefaultOption($key)
    {
        return Container::getInstance()->getOptions()->getDefaultOption($key);
    }

    /**
     * fvcnGetOption()
     *
     * @version 20120710
     * @param string $key
     * @return mixed
     */
    public static function fvcnGetOption($key)
    {
        return Container::getInstance()->getOptions()->getOption($key);
    }

    /**
     * fvcnAddOptions()
     *
     * @version 20120710
     */
    public static function fvcnAddOptions()
    {
        Container::getInstance()->getOptions()->addOptions();

        do_action('fvcn_add_options');
    }

    /**
     * fvcnDeleteOptions()
     *
     * @version 20120710
     */
    public static function fvcnDeleteOptions()
    {
        Container::getInstance()->getOptions()->deleteOptions();

        do_action('fvcn_delete_options');
    }
}
