<?php

/**
 * FV_Container
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
abstract class FV_Container extends FV_Singleton
{
	/**
     * Object options.
	 * @var array
	 */
	protected $_options = [];

	/**
     * Saved objects.
	 * @var array
	 */
	protected $_objects = [];

	/**
	 * Constructor. Optional set options.
	 *
	 * @param array $options
     * @return void
	 */
	public function __construct(array $options= [])
	{
		$this->_options = $options;
	}
}
