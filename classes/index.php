<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once 'api.php';
require_once 'actions.php';
require_once 'cron.daily.php';
require_once 'avatar.php';

require_once 'admin/index.php';

class CW_Classes {  
  static $instance = null;
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_Classes();
    }
    return self::$instance;
  }
  private function __construct() {
    CW_Api::getInstance();
    CW_Cron_Daily::getInstance();
    CW_GeneralSettings::getInstance();
  }
}