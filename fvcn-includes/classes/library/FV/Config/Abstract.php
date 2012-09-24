<?php

/**
 * FV_Config_Abstract
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
abstract class FV_Config_Abstract implements Iterator
{
    /**
     * Get a config item.
     *
     * @param string $key
     * @return mixed
     */
    abstract public function get($key);





  private $myArray;

  public function __construct( $givenArray ) {
    $this->myArray = $givenArray;
  }
  function rewind() {
    return reset($this->myArray);
  }
  function current() {
    return current($this->myArray);
  }
  function key() {
    return key($this->myArray);
  }
  function next() {
    return next($this->myArray);
  }
  function valid() {
    return key($this->myArray) !== null;
  }
}
