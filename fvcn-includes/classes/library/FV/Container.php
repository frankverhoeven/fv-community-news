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
	protected $_options = array();

	/**
     * Saved objects.
	 * @var array
	 */
	protected $_objects = array();

	/**
	 * Constructor. Optional set options.
	 *
	 * @param array $options
     * @return void
	 */
	public function __construct(array $options=array())
	{
		$this->_options = $options;
	}
}
