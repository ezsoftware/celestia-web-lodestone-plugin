<?php
/**
 * Plugin Name: Lodestone Scraper
 * Description: Lodestone Scraper by Celestia FC on Hyperion
 * Version: 0.5 (05/15/2017)
 * Author: John Ryan - EZ Software Inc
 * Author URI: http://www.example.com
 */

/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once 'classes/index.php';

define("CW_LS_ABSPATH", plugin_dir_path(__FILE__));
define("CW_LS_URLPATH", plugin_dir_url(__FILE__));
 
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
    register_activation_hook(__FILE__, array('CW_LS_Cron_Daily', 'register_cron_hook'));
    register_deactivation_hook(__FILE__, array('CW_LS_Cron_Daily', 'deregister_cron_hook'));

    add_action( 'wp_enqueue_scripts', array($this, 'enqueue_styles') );
    add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_styles') );
  }

  public function enqueue_styles() {
    wp_enqueue_style( 'cw_ls-style', CW_LS_URLPATH . 'css/styles.css');
	}

  public function admin_enqueue_styles() {
    wp_enqueue_style( 'cw_ls_admin-style', CW_LS_URLPATH . 'css/admin/styles.css');
  }
} 

CW_LS_Lodestone::getInstance();