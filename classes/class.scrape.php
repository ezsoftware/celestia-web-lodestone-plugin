<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
// Celestia ID: 9236742098248532608
require_once 'class.request.php';

class CW_LS_Scraper {
  static $instance = null;
  static $search_url = 'https://na.finalfantasyxiv.com/lodestone/character/?q={first_name}+{last_name}&worldname={server}';
  static $profile_url = 'https://na.finalfantasyxiv.com/lodestone/character/{character_id}/';
  static $fc_members_url = 'https://na.finalfantasyxiv.com/lodestone/freecompany/{free_company_id}/member/';
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_LS_Scraper();
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
	if($nodes)
		return $nodes->item(0);
	return null;
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

  private function generateClassXPathSelector($element, $class, $extra) {
    return "//{$element}[contains(concat(' ', normalize-space(@class), ' '), '{$class}')]{$extra}";
  }

  private function get_character_class_levels($dom) {
    $classes = array();
    $nodes = $this->findNodes($dom, $this->generateClassXPathSelector('div', 'character__level__list', '/ul/li'));
    foreach($nodes as $node) {
      $tmp_dom = new DomDocument();
      $tmp_dom->appendChild($tmp_dom->importNode($node, true));
      $classes[$this->getNodeAttribute($tmp_dom, '//img', 'data-tooltip')] = trim($node->nodeValue);
    }
    return $classes;
  }

  public function search($first_name, $last_name, $server = '') {
    $results = array();
    $page = 1;
    $url = str_replace('{first_name}', $first_name, self::$search_url);
    $url = str_replace('{last_name}', $last_name, $url);
    $url = str_replace('{server}', $server, $url);
    $request = CW_LS_Request::getInstance();
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
          'face' => $this->getNodeAttribute($tmp_dom, '//div[@class="entry__chara__face"]/img', 'src'),
          'name' => $this->getNodeValue($tmp_dom, '//p[@class="entry__name"]'),
          'world' => $this->getNodeValue($tmp_dom, '//p[@class="entry__world"]'),
          'free_company' => $this->getNodeValue($tmp_dom, '//a[@class="entry__freecompany__link"]/span')
        );
      }
      $page++;
    } while($nodes->length > 0);
    return $results;
  }

  public function get_character_profile($character_id) {
    $url = str_replace('{character_id}', $character_id, self::$profile_url);
    $request = CW_LS_Request::getInstance();
    $html = $request->get($url);
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $result = array(
      'name' => $this->getNodeValue($dom, '//p[@class="frame__chara__name"]'),
      'title' => $this->getNodeValue($dom, '//p[@class="frame__chara__title"]'),
      'world' => $this->getNodeValue($dom, '//p[@class="frame__chara__world"]'),
      'classes' => $this->get_character_class_levels($dom),
      'free_company' => $this->getNodeValue($dom, '//div[@class="character__freecompany__name"]/h4/a'),
      'face' => $this->getNodeAttribute($dom, '//div[@class="frame__chara__face"]/img', 'src'),
      'image' => $this->getNodeAttribute($dom, '//div[@class="character__detail__image"]/a/img', 'src')
    );
    return $result;
  }

  public function get_member_list($free_company_id) {
    $results = array();
    $page = 1;
    $url = str_replace('{free_company_id}', $free_company_id, self::$fc_members_url);
    $request = CW_LS_Request::getInstance();
    do{
      $html = $request->get($url . '?page=' . $page);
      $dom = new DOMDocument();
      $dom->loadHTML($html);
      $finder = new DomXPath($dom);
      $nodes = $finder->query('//li[@class="entry"]');
      foreach($nodes as $node) {
        $tmp_dom = new DomDocument();
        $tmp_dom->appendChild($tmp_dom->importNode($node, true));
        $results[] = array(
          'character_id' => $this->search_character_id($tmp_dom),
          'face' => $this->getNodeAttribute($tmp_dom, '//div[@class="entry__chara__face"]/img', 'src'),
          'name' => $this->getNodeValue($tmp_dom, '//p[@class="entry__name"]'),
          'world' => $this->getNodeValue($tmp_dom, '//p[@class="entry__world"]'),
          'rank_icon' => $this->getNodeAttribute($tmp_dom, '//ul[@class="entry__freecompany__info"]/li/img', 'src'),
          'rank' => $this->getNodeValue($tmp_dom, '//ul[@class="entry__freecompany__info"]/li/span')
        );
      }
      $page++;
    } while($nodes->length > 0);
    return $results;
  }
}