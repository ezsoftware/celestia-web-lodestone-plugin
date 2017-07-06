<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class CW_LS_Members_List {
  static $instance = null;
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_LS_Members_List();
    }
    return self::$instance;
  }

  static $templateList = null;
  static $templateListItem = null;
  private static function getTemplates() {
    if(self::$templateList === null) {
      self::$templateList = file_get_contents(CW_LS_ABSPATH . 'templates/members/list.html');
      self::$templateListItem = file_get_contents(CW_LS_ABSPATH . 'templates/members/list-item.html');
    }

    return (object) array(
      'list' => self::$templateList,
      'listItem' => self::$templateListItem
    );
  }
  private function __construct() {
    add_shortcode("cw_ls_member_list", array($this, 'show_member_list'));
  }

  function getMemberLink($member) {
    return "";
  }

  function rankToValue($rank) {
    switch(strtolower($rank)) {
      case "king":
        return 1;
      case "queen":
        return 2;
      case "kings hand":
        return 3;
      case "magistrate":
        return 4;
      case "archduke":
        return 5;
      case "baron":
        return 6;
      case "knight":
        return 7;
      case "soldier":
        return 8;
      default:
        return 9;
    }
  }

  function cmp($a, $b)
  {
    $rank = self::rankToValue($a['rank']) - self::rankToValue($b['rank']);
    if($rank === 0) {
      return strcmp($a['name'], $b['name']);
    }
    return $rank;
  }

  public function show_member_list() {
    $templates = self::getTemplates();
    
    $memberList = get_option('fc_member_data');
    if($memberList === false) {
      return "<h3>Invalid or Non-existant FC Membership Data</h3>";
    }

    usort($memberList, array($this, 'cmp'));
    $memberListItemHTML = "";
    foreach($memberList as $index => $member) {
      $listHtml = json_decode(json_encode($templates->listItem));
      $listHtml = str_replace($member['character_id'], '{character_id}', $listHtml);
      $listHtml = str_replace($member['name'], '{character_name}', $listHtml);
      $listHtml = str_replace(self::getMemberLink($member), '{member_view_icon}', $listHtml);
      $listHtml = str_replace($member['rank'], '{rank}', $listHtml);
    }
  }
}