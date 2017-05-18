<?php

class CW_Request {
  static $instance = null;
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_Request();
    }
    return self::$instance;
  }
  private function __construct() {

  }

  private function getResponse($url, $ops) {
    $ops = $ops ?: array();
    $curl = curl_init();
    $final_ops = array_merge(array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => 1
    ), $ops);
    curl_setopt_array($curl, $final_ops);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
  }

  public function get($url) {
    return self::getResponse($url);
  }

  public function post($url, $postFields) {
    return self::getResponse($url, array(
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $postFields
    ));
  }
}