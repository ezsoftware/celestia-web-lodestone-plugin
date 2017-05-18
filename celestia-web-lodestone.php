<?php
/**
 * Plugin Name: Lodestone Scraper
 * Description: Lodestone Scraper by Celestia FC on Hyperion
 * Version: 0.1 (05/15/2017)
 * Author: John Ryan - EZ Software Inc
 * Author URI: http://www.example.com
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require('classes/index.php');
 
class CW_Lodestone {    
  static $instance = null;
  public static function getInstance() {
    if($instance === null) {
      $instance = new CW_Lodestone();
    }
    return $instance;
  }
  private function __construct() {
    CW_Api::getInstance();
  }
} 

CW_Lodestone::getInstance();