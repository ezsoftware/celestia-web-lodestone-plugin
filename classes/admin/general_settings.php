<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class CW_GeneralSettings {  
  static $instance = null;
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_GeneralSettings();
    }
    return self::$instance;
  }
  private function __construct() {
    add_filter( 'admin_init' , array( $this , 'register_fields' ) );
  }

  public function register_fields() {
    register_setting( 'general', 'fc_lodestone_id', 'esc_attr' );
    add_settings_field('fc_lodestone_id', '<label for="fc_lodestone_id">'.__('Free Company Lodestone ID:' , 'fc_lodestone_id' ).'</label>' , array($this, 'fc_lodestone_id_html') , 'general' );
  }

  public function fc_lodestone_id_html() {
    $value = get_option( 'fc_lodestone_id', '' );
    echo '<input type="text" id="fc_lodestone_id" name="fc_lodestone_id" value="' . $value . '" />';
  }
}