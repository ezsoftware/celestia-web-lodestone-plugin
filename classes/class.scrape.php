<?php

require_once 'class.request.php';

class CW_Scraper {
  static $instance = null;
  static $search_url = 'http://na.finalfantasyxiv.com/lodestone/character/?q={first_name}+{last_name}&worldname={server}';
  public static function getInstance() {
    if($instance === null) {
      $instance = new CW_Scraper();
    }
    return $instance;
  }
  private function __construct() {

  }

  public function search($firstName, $lastName, $server) {
    $server = $server ?: '';
    $url = str_replace('{first_name}', $firstName, self::$search_url);
    $url = str_replace('{last_name}', $lastName, $url);
    $url = str_replace('{server}', $server, $url);
    $request = CW_Request::getInstance();
    $html = $request->get($url);
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $finder = new DomXPath($dom);
    $nodes = $finder->query('//*[contains(@class, "entry")]');
    $tmp_dom = new DomDocument();
    foreach($nodes as $node) {
      $tmp_dom->appendChild($tmp_dom->importNode($node, true));
    }
    $resultHTML = trim($tmp_dom->saveHTML());
    return $resultHTML;
  }
}