<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once 'general_settings.php';
require_once 'user_profile.php';

class CW_LS_Classes_Admin {  
  static $instance = null;
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_LS_Classes_Admin();
    }
    return self::$instance;
  }
  private function __construct() {
    CW_LS_GeneralSettings::getInstance();
    CW_LS_UserProfile::getInstance();
  }
}