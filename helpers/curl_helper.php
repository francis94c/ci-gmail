<?php
declare(strict_types=1);
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('build_url_query')) {
  function build_url_query(array $params, bool $urlEncode=true) {
    $queryString = '?';
    foreach($params as $key => $val) {
      $queryString .= $key."=".($urlEncode ? rawurlencode($val) : $val)."&";
    }
    return substr($queryString, 0, strlen($queryString) - 1);
  }
}
