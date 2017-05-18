<?php

require_once 'class.scrape.php';

class CW_Api {  
  static $instance = null;
  public static function getInstance() {
    if($instance === null) {
      $instance = new CW_Api();
    }
    return $instance;
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

  public function cw_validateCharacterName() {
    $scraper = CW_Scraper::getInstance();
    $response = $scraper->search($_GET['first_name'], $_GET['last_name'], $_GET['server']);
    echo $response;
    wp_die();
  }
}