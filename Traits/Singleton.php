<?php
namespace teik\Theme\Traits;

trait Singleton {
  protected static $instance = NULL;
  public static function instance() {
    // create an object
    NULL === self::$instance and self::$instance = new static;
    return self::$instance; // return the object
  }

  private function __construct() 
  {

  }

  private function __clone() 
  {
    
  }
}