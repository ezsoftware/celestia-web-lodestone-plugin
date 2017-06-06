<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class CW_Avatar {  
  static $instance = null;
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_Avatar();
    }
    return self::$instance;
  }
  private function __construct() {
    add_filter('pre_get_avatar', array($this, 'get_user_avatar_url'), 10, 3);

    add_action('update_avatar', array($this, 'update_user_avatar'), 10, 2);
  }

  public function get_user_avatar_url($avatar, $id_or_email, $args) {
    $mediaId = get_user_meta($id_or_email, 'avatar_media_id', true);
    $avatar_url = wp_get_attachment_image_url($mediaId);
    if($avatar_url) {
      $avatar = $avatar_url;
    }
    return $avatar;
  }

  public function update_user_avatar($url, $id) {
    $user_info = get_userdata($id);
    //https://codex.wordpress.org/Function_Reference/media_handle_sideload
    	// Need to require these files
    if ( !function_exists('media_handle_upload') ) {
      require_once(ABSPATH . "wp-admin" . '/includes/image.php');
      require_once(ABSPATH . "wp-admin" . '/includes/file.php');
      require_once(ABSPATH . "wp-admin" . '/includes/media.php');
    }

    $tmp = download_url( $url );
    if( is_wp_error( $tmp ) ){
      // download failed, handle error
    }
    $post_id = 0;
    $desc = $user_info->first_name . ' ' . $user_info->last_name . ' lodestone avatar';
    $file_array = array();

    // Set variables for storage
    // fix file filename for query strings
    preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches);
    $file_array['name'] = basename($matches[0]);
    $file_array['tmp_name'] = $tmp;

    // If error storing temporarily, unlink
    if ( is_wp_error( $tmp ) ) {
      @unlink($file_array['tmp_name']);
      $file_array['tmp_name'] = '';
    }

    // do the validation and storage stuff
    $mediaId = media_handle_sideload( $file_array, $post_id, $desc );

    // If error storing permanently, unlink
    if ( is_wp_error($mediaId) ) {
      @unlink($file_array['tmp_name']);
      return null;
    }

    $existingAvatar = get_user_meta($id, 'avatar_media_id', true);
    if($existingAvatar) {
      wp_delete_attachment($existingAvatar);
    }
    update_user_meta($id, 'avatar_media_id', $mediaId, $existingAvatar);
  }
}