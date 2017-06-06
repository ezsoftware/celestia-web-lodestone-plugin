<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once 'class.scrape.php';

require_once 'cron.daily.php';

class CW_LS_Api {  
  static $instance = null;
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_LS_Api();
    }
    return self::$instance;
  }
  private function __construct() {
    $this->register_filters();
    $this->register_actions();
  }

  function register_filters() {

  }

  function register_actions() {
    self::addAction('cw_searchCharacter');
    self::addAction('cw_getCharacterProfile');
    self::addAction('cw_getMemberList');
    self::addAction('cw_run_cron');
  }

  private function addAction($action) {
    add_action("wp_ajax_$action", array($this, $action));
    add_action("wp_ajax_nopriv_$action", array($this, $action));
  }

  private function getQS($key, $default = "") {
    $value = $default;
    if(isset($_GET[$key]))
      $value = str_replace('{apostrophe}', '\'', $_GET[$key]);
    if(isset($_POST[$key]))
      $value = str_replace('{apostrophe}', '\'', $_POST[$key]);
    return $value;
  }

  public function cw_searchCharacter() {
    $scraper = CW_LS_Scraper::getInstance();
    $response = $scraper->search($this->getQS('first_name'), $this->getQS('last_name'), $this->getQS('server'));
    $res_str = json_encode($response);
    echo $res_str;
    wp_die();
  }

  public function cw_getCharacterProfile() {
    $scraper = CW_LS_Scraper::getInstance();
    $response = $scraper->get_character_profile($this->getQS('character_id'));
    $res_str = json_encode($response);
    echo $res_str;
    wp_die();
  }

  public function cw_getMemberList() {
    $scraper = CW_LS_Scraper::getInstance();
    $response = $scraper->get_member_list($this->getQS('free_company_id'));
    $res_str = json_encode($response);
    echo $res_str;
    wp_die();
  }

  public function cw_run_cron() {
    $cron = CW_LS_Cron_Daily::getInstance();
    $cron->do_daily_data_sync();
    wp_die();
  }
}