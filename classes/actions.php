<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once "class.scrape.php";

// Gives us access to the download_url() and wp_handle_sideload() functions
require_once( ABSPATH . 'wp-admin/includes/file.php' );

class CW_LS_Actions {  
  static $instance = null;
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_LS_Actions();
    }
    return self::$instance;
  }
  private function __construct() {
    add_filter("cw-lodestone-get-character", array($this, 'get_character_profile'), 10, 2);
    add_filter("cw-lodestone-get-fc-list", array($this, 'get_free_company_list'), 10, 2);
    add_filter("cw-lodestone-search", array($this, 'search_character'), 10, 4);
  }

  public function get_character_profile($character_data, $character_id) {
    $scraper = CW_LS_Scraper::getInstance();
    $new_data = $scraper->get_character_profile($character_id);

    if($character_data != null) {
      $new_data = array_replace($character_data, $new_data);
    }

    return $new_data;
  }

  public function get_free_company_list($fc_data, $free_company_id) {
    $scraper = CW_LS_Scraper::getInstance();
    $new_data = $scraper->get_member_list($free_company_id);

    if($fc_data != null) {
      $new_data = array_replace($fc_data, $new_data);
    }

    return $fc_data;
  }

  public function search_character($search_data, $first_name, $last_name, $world) {
    $scraper = CW_LS_Scraper::getInstance();
    $new_data = $scraper->search($first_name, $last_name, $world);

    if($search_data != null) {
      $new_data = array_replace($search_data, $new_data);
    }

    return $new_data;
  }
}