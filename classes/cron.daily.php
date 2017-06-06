<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once 'class.scrape.php';
require_once 'avatar.php';

class CW_LS_Cron_Daily {  
  static $instance = null;
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_LS_Cron_Daily();
    }
    return self::$instance;
  }
  private function __construct() {
    add_action('cw_lodestone_daily_data_sync', array($this, 'do_daily_data_sync'));
  }

  static function register_cron_hook() {
    if(!wp_next_scheduled('cw_lodestone_daily_event')) {
      wp_schedule_event(time(), 'daily', 'cw_lodestone_daily_data_sync');
    }
  }

  static function deregister_cron_hook() {
    wp_clear_scheduled_hook('cw_lodestone_daily_data_sync');
  }

  public function do_daily_data_sync() {
    if(!class_exists('WP_User')) {
      require_once(ABSPATH . 'wp-includes/class-wp-user.php');
    }
    $scraper = CW_LS_Scraper::getInstance();
    $avatar = CW_LS_Avatar::getInstance();
    $fc_id = get_option( 'fc_lodestone_id', '' );
    $fc_member_list = $scraper->get_member_list($fc_id);
    update_option('fc_member_data', $fc_member_list);
    $users = get_users(array('fields' => array('ID')));
    foreach($users as $user) {
      $user_id = $user->ID;
      $character_id = get_user_meta($user_id, 'character_id', true);
      if($character_id) {
        $characterData = $scraper->get_character_profile($character_id);
        update_user_meta($user_id, 'character_profile', $characterData);
        update_user_meta($user_id, 'profile_last_updated', date("Y-m-d H:i:s"));
        $names = explode(' ', $characterData['name']);
        update_user_meta($user_id, 'first_name', $names[0]);
        update_user_meta($user_id, 'last_name', $names[1]);
        update_user_meta($user_id, 'nickname', $characterData['name']);
        $avatar->update_user_avatar($characterData['face'], $user_id);
        $member_data = self::get_member_rank($fc_member_list, $character_id);
        var_dump($member_data);
        $rank = 'ally';
        if($member_data != null) {
          $rank = self::role_to_stub($member_data['rank']);
        }
        var_dump($rank);
        if(!self::is_user_in_role($user_id, $rank)) {
          $u = new WP_User($user_id);
          $user_roles = self::get_user_roles_by_user_id($user_id);
          foreach($user_roles as $user_role) {
            if(self::is_rank($user_role)) {
              $u->remove_role($user_role);
            }
          }
          $u->add_role($rank);
        }
      }
    }
  }

  private function is_rank($rank) {
    return ( $rank == 'applicant'
      || $rank == 'archduke'
      || $rank == 'baron'
      || $rank == 'king'
      || $rank == 'kings_hand'
      || $rank == 'magistrate'
      || $rank == 'queen'
      || $rank == 'recruit'
      || $rank == 'soldier'
      || $rank == 'ally'
    );
  }

  private function role_to_stub($role) {
    $role = strtolower($role);
    $role = str_replace(' ', '_', $role);
    $role = preg_replace('[^0-9a-z_]', '', $role);
  }

  private function get_member_rank($fc_member_list, $character_id) {
    foreach($fc_member_list as $member) {
      if($member['character_id'] == $character_id) {
        return $member;
      }
    }
    return null;
  }

  private function get_user_roles_by_user_id( $user_id ) {
    $user = get_userdata( $user_id );
    return empty( $user ) ? array() : $user->roles;
  }

  private function is_user_in_role( $user_id, $role  ) {
    return in_array( $role, self::get_user_roles_by_user_id( $user_id ) );
  }
}