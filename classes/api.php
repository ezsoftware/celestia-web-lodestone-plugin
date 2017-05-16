<?php

class CW_Api {
  function __construct() {
    $this->register_filters();
    $this->register_actions();
  }

  function register_filters() {

  }

  function register_actions() {
    add_action( 'wp_ajax_cw_validateCharacterName', array($this, 'cw_validateCharacterName') );
  }
}

new CW_Api();