<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once 'list.php';

class CW_LS_Members {  
  static $instance = null;
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_LS_Members();
    }
    return self::$instance;
  }
  private function __construct() {
    CW_LS_Members_List::getInstance();
  }
}