<?php

require_once 'class.scrape.php';

class CW_Api {  
  static $instance = null;
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_Api();
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
    self::addAction('cw_validateCharacterName');
  }

  private function addAction($action) {
    add_action("wp_ajax_$action", array($this, $action));
    add_action("wp_ajax_nopriv_$action", array($this, $action));
  }

  private function getQS($key, $default) {
    $default = $default ?: '';
    $value = $default;
    if(isset($_GET[$key]))
      $value = $_GET[$key];
    return $value;
  }

  public function cw_validateCharacterName() {
    $scraper = CW_Scraper::getInstance();
    $response = $scraper->search($this->getQS('first_name'), $this->getQS('last_name'), $this->getQS('server'));
    echo $response;
    wp_die();
  }
}