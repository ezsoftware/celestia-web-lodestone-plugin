<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once "class.scrape.php";

// Gives us access to the download_url() and wp_handle_sideload() functions
require_once( ABSPATH . 'wp-admin/includes/file.php' );

class CW_Actions {  
  static $instance = null;
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_Actions();
    }
    return self::$instance;
  }
  private function __construct() {
    add_filter("cw-lodestone-get-character", array($this, 'get_character_profile'), 10, 1);
    add_filter("cw-lodestone-get-fc-list", array($this, 'get_free_company_list'), 10, 1);
    add_filter("cw-lodestone-search", array($this, 'search_character'), 10, 3);
  }

  public function get_character_profile($character_id) {
    $scraper = CW_Scraper::getInstance();
    return $scraper->get_character_profile($character_id);
  }

  public function get_free_company_list($free_company_id) {
    $scraper = CW_Scraper::getInstance();
    return $scraper->get_member_list($free_company_id);
  }

  public function search_character($first_name, $last_name, $world) {
    $scraper = CW_Scraper::getInstance();
    return $scraper->search($first_name, $last_name, $world);
  }
}