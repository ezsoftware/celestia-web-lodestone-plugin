<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class CW_LS_Member_List {
  static $instance = null;
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_LS_Member_List();
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

  public function show_member_list() {
    $templates = self::getTemplates();
    
    $memberList = get_option('fc_member_data');
    if($memberList === false) {
      return "<h3>Invalid or Non-existant FC Membership Data</h3>";
    }

    var_dump($memberList);
  }
}