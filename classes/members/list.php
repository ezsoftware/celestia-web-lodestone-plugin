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
  static $templateListItemClassItem = null;
  private static function getTemplates() {
    if(self::$templateList === null) {
      self::$templateList = file_get_contents(CW_LS_ABSPATH . 'templates/members/list.html');
      self::$templateListItem = file_get_contents(CW_LS_ABSPATH . 'templates/members/list-item.html');
      self::$templateListItemClassItem = file_get_contents(CW_LS_ABSPATH . 'templates/members/list-item-class-item.html');
    }

    return (object) array(
      'list' => self::$templateList,
      'listItem' => self::$templateListItem,
      'listItemClassItem' => self::$templateListItemClassItem
    );
  }
  private function __construct() {
    add_shortcode("cw_ls_member_list", array($this, 'show_member_list'));
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
      case "high priestess":
        return 4.5;
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
      case "alt":
        return 10;
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

  function getUsersCharacters() {
    global $wpdb;
    $query = "SELECT u.ID, um.meta_value as 'character_id', ump.meta_value as 'character_profile' FROM `{$wpdb->prefix}users` u INNER JOIN {$wpdb->prefix}usermeta um ON u.id = um.user_id AND um.meta_key = 'character_id' INNER JOIN {$wpdb->prefix}usermeta ump ON u.id = ump.user_id AND ump.meta_key = 'character_profile'";

    $data = $wpdb->get_results($query,OBJECT_K);
    return $data;
  }

  function getUsersCharacter($users, $character_id) {
    foreach($users as $user_id => $user){
      if($user->character_id == $character_id) {
        return $user;
      }
    }
    return null;
  }

  function getUsersCharacterData($user) {
    if($user !== null) {
      return unserialize($user->character_profile);
    }
    return null;
  }

  function getMemberLink($member, $character) {
    if($character !== null) {
      $avatar = get_avatar($character->ID, 16);
      return "<a href='/members/{$member['character_id']}'>$avatar</a>";
    }
    return "";
  }

  function getMemberClassList($user) {
    $templates = self::getTemplates();
    $jobsHTML = "";
    if($user !== null) {
      $data = $this->getUsersCharacterData($user);
      foreach($data['classes'] as $key => $value) {
        $classJob = strtolower($key);
        $classJobs = explode('/', $classJob);
        $classJob = trim($classJobs[0]);
        $newJobHTML = str_replace('{job}', str_replace(' ', '_', $classJob), $templates->listItemClassItem);
        $newJobHTML = str_replace('{Job}', ucwords($key), $newJobHTML);
        $newJobHTML = str_replace('{level}', $value, $newJobHTML);
        $newJobHTML = str_replace('{capped}', (intval($value) >= 70 ? 'capped' : ''), $newJobHTML);
        $jobsHTML .= $newJobHTML;
      }
    }
    return $jobsHTML;
  }

  public function show_member_list() {
    $templates = self::getTemplates();
    
    $memberList = get_option('fc_member_data');
    if($memberList === false) {
      return "<h3>Invalid or Non-existant FC Membership Data</h3>";
    }
    $users = $this->getUsersCharacters();

    usort($memberList, array($this, 'cmp'));
    $memberListItemHTML = "";
    foreach($memberList as $member) {
      $user = $this->getUsersCharacter($users, $member['character_id']);
      $listHtml = str_replace('{character_id}', $member['character_id'], $templates->listItem);
      $listHtml = str_replace('{character_name}', $member['name'], $listHtml);
      $listHtml = str_replace('{member_view_icon}', $this->getMemberLink($member, $user), $listHtml);
      $listHtml = str_replace('{member_view_class_list}', $this->getMemberClassList($user), $listHtml);
      $listHtml = str_replace('{rank}', $member['rank'], $listHtml);
      $memberListItemHTML .= $listHtml;
    }
    return str_replace('{list-items}', $memberListItemHTML, $templates->list);
  }
}