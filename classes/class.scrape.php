<?php

require_once 'class.request.php';

class CW_Scraper {
  static $instance = null;
  static $search_url = 'http://na.finalfantasyxiv.com/lodestone/character/?q={first_name}+{last_name}&worldname={server}';
  static $profile_url = 'http://na.finalfantasyxiv.com/lodestone/character/{character_id}/';
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_Scraper();
    }
    return self::$instance;
  }
  private function __construct() {
	libxml_use_internal_errors(true);
  }

  private function findNodes($dom, $query) {
    $finder = new DomXPath($dom);
    $nodes = $finder->query($query);
    return $nodes;
  }

  private function findNode($dom, $query) {
    $nodes = $this->findNodes($dom, $query);
    return $nodes->item(0);
  }
  
  private function getNodeValue($dom, $query) {
    $node = $this->findNode($dom, $query);
    if(isset($node))
      return $node->nodeValue;
    return "";
  }

  private function getNodeAttribute($dom, $query, $attribute) {
    $node = $this->findNode($dom, $query);
    if(isset($node))
      return $node->getAttribute($attribute);
    return "";
  }

  private function search_character_id($dom) {
    $character_url = $this->getNodeAttribute($dom, '//a[starts-with(@href, "/lodestone/character/")]', 'href');
    $chunks = explode('/', $character_url);
    return $chunks[3];
  }

  private function search_character_face($dom) {
    return $this->getNodeAttribute($dom, '//div[@class="entry__chara__face"]/img', 'src');
  }

  private function search_character_name($dom) {
    return $this->getNodeValue($dom, '//p[@class="entry__name"]');
  }

  private function search_character_world($dom) {
    return $this->getNodeValue($dom, '//p[@class="entry__world"]');
  }
  
  private function search_character_free_company($dom) {
    return $this->getNodeValue($dom, '//a[@class="entry__freecompany__link"]/span');
  }

  public function search($firstName, $lastName, $server = '') {
    $results = array();
    $page = 1;
    $url = str_replace('{first_name}', $firstName, self::$search_url);
    $url = str_replace('{last_name}', $lastName, $url);
    $url = str_replace('{server}', $server, $url);
    $request = CW_Request::getInstance();
    do{
      $html = $request->get($url . '&page=' . $page);
      $dom = new DOMDocument();
      $dom->loadHTML($html);
      $finder = new DomXPath($dom);
      $nodes = $finder->query('//div[@class="entry"]');
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
      $page++;
    } while($nodes->length > 0);
    return $results;
  }

  private function get_character_class($dom) {
    return $this->getNodeAttribute($dom, '//img', 'data-tooltip');
  }

  private function get_character_class_levels($dom) {
    $classes = array();
    $nodes = $this->findNodes($dom, '//div[@class="character__level__list"]/ul/li');
    foreach($nodes as $node) {
      $tmp_dom = new DomDocument();
      $tmp_dom->appendChild($tmp_dom->importNode($node, true));
      $classes[$this->get_character_class($tmp_dom)] = trim($node->nodeValue);
    }
    return $classes;
  }


  private function get_character_free_company($dom) {
    return $this->getNodeValue($dom, '//div[@class="character__freecompany__name"]/h4/a');
  }
  private function get_character_name($dom) {
    return $this->getNodeValue($dom, '//p[@class="frame__chara__name"]');
  }
  private function get_character_title($dom) {
    return $this->getNodeValue($dom, '//p[@class="frame__chara__title"]');
  }
  private function get_character_world($dom) {
    return $this->getNodeValue($dom, '//p[@class="frame__chara__world"]');
  }
  private function get_character_face($dom) {
    return $this->getNodeAttribute($dom, '//div[@class="frame__chara__face"]/img', 'src');
  }
  private function get_character_image($dom) {
    return $this->getNodeAttribute($dom, '//div[@class="character__detail__image"]/a/img', 'src');
  }

  public function get_character_profile($characterId) {
    $url = str_replace('{character_id}', $characterId, self::$profile_url);
    $request = CW_Request::getInstance();
    $html = $request->get($url);
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $result = array(
      'name' => $this->get_character_name($dom),
      'title' => $this->get_character_title($dom),
      'world' => $this->get_character_world($dom),
      'classes' => $this->get_character_class_levels($dom),
      'free_company' => $this->get_character_free_company($dom),
      'face' => $this->get_character_face($dom),
      'image' => $this->get_character_image($dom)
    );
    return $result;
  }
}