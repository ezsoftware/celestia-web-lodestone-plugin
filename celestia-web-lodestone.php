<?php
/**
 * Plugin Name: Lodestone Scraper
 * Description: Lodestone Scraper by Celestia FC on Hyperion
 * Version: 0.5 (05/15/2017)
 * Author: John Ryan - EZ Software Inc
 * Author URI: http://www.example.com
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require('classes/index.php');
 
class CW_LS_Lodestone {    
  static $instance = null;
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_LS_Lodestone();
    }
    return self::$instance;
  }
  private function __construct() {
    CW_LS_Classes::getInstance();
    register_activation_hook(__FILE__, array('CW_Cron_Daily', 'register_cron_hook'));
    register_deactivation_hook(__FILE__, array('CW_Cron_Daily', 'deregister_cron_hook'));
  }
} 

CW_LS_Lodestone::getInstance();