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

  private function getResponse($url, $ops = array()) {
    $curl = curl_init();
    $final_ops = array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_USERAGENT => '5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
      CURLOPT_SSL_VERIFYPEER => FALSE,
      CURLOPT_SSL_VERIFYHOST => FALSE
    ) + $ops;
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