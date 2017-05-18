<?php

require_once 'class.request.php';

class CW_Scraper {
  static $instance = null;
  static $search_url = 'http://na.finalfantasyxiv.com/lodestone/character/?q={first_name}+{last_name}&worldname={server}';
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_Scraper();
    }
    return self::$instance;
  }
  private function __construct() {
	libxml_use_internal_errors(true);
  }

  private function search_character_id($dom) {
    $e_finder = new DomXPath($dom);
    $nodes = $e_finder->query('//a[starts-with(@href, "/lodestone/character/")]');
    $node = $nodes->item(0);
    $characterUrl = $node->getAttribute('href');
    $chunks = explode('/', $characterUrl);
    return $chunks[3];
  }

  private function search_character_face($dom) {
    $e_finder = new DomXPath($dom);
    $nodes = $e_finder->query('//div[@class="entry__chara__face"]/img');
    $node = $nodes->item(0);
    return $node->getAttribute('src');
  }

  private function search_character_name($dom) {
    $e_finder = new DomXPath($dom);
    $nodes = $e_finder->query('//p[@class="entry__name"]');
    $node = $nodes->item(0);
    return $node->nodeValue;
  }

  private function search_character_world($dom) {
    $e_finder = new DomXPath($dom);
    $nodes = $e_finder->query('//p[@class="entry__world"]');
    $node = $nodes->item(0);
    return $node->nodeValue;
  }
  
  private function search_character_free_company($dom) {
    $e_finder = new DomXPath($dom);
    $nodes = $e_finder->query('//a[@class="entry__freecompany__link"]/span');
    $node = $nodes->item(0);
    if(isset($node))
      return $node->nodeValue;
    return "";
  }

  public function search($firstName, $lastName, $server = '') {
    $url = str_replace('{first_name}', $firstName, self::$search_url);
    $url = str_replace('{last_name}', $lastName, $url);
    $url = str_replace('{server}', $server, $url);
    $request = CW_Request::getInstance();
    $html = $request->get($url);
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $finder = new DomXPath($dom);
    $nodes = $finder->query('//div[@class="entry"]');
    $results = array();
    foreach($nodes as $node) {
	  $tmp_dom = new DomDocument();
      $tmp_dom->appendChild($tmp_dom->importNode($node, true));
      $results[] = array(
        'id' => $this->search_character_id($tmp_dom),
        'face' => $this->search_character_face($tmp_dom),
        'name' => $this->search_character_name($tmp_dom),
        'world' => $this->search_character_world($tmp_dom),
        'free_company' => $this->search_character_free_company($tmp_dom)
      );
    }
    return $results;
  }
}