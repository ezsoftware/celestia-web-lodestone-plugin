<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once('class.scrape.php');

class CW_Cron_Daily {  
  static $instance = null;
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_Cron_Daily();
    }
    return self::$instance;
  }
  private function __construct() {
    add_action('cw_lodestone_daily_data_sync', array($this, 'do_daily_data_sync'));
  }

  static function register_cron_hook() {
    if(!wp_next_scheduled('cw_lodestone_daily_event')) {
      wp_schedule_event(time(), 'daily', 'cw_lodestone_daily_data_sync');
    }
  }

  static function deregister_cron_hook() {
    wp_clear_scheduled_hook('cw_lodestone_daily_data_sync');
  }

  public function do_daily_data_sync() {
    $users = get_users(array('fields' => array('ID')));
    foreach($users as $user_id) {
      $characterId = get_user_meta($user_id->ID, 'character_id', true);
      if($characterId) {
        $scraper = CW_Scraper::getInstance();
        $characterData = $scraper->get_character_profile($characterId);
        update_user_meta($user_id->ID, 'character_profile', $characterData);
      }
    }
  }
}