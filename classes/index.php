<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once 'api.php';
require_once 'actions.php';
require_once 'cron.daily.php';
require_once 'avatar.php';

require_once 'admin/index.php';
require_once 'members/index.php';

class CW_LS_Classes {  
  static $instance = null;
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_LS_Classes();
    }
    return self::$instance;
  }
  private function __construct() {
    CW_LS_Api::getInstance();
    CW_LS_Cron_Daily::getInstance();
    CW_LS_Avatar::getInstance();

    CW_LS_Admin::getInstance();
    CW_LS_Members::getInstance();
  }
}