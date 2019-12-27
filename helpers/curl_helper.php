<?php
declare(strict_types=1);
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('build_url_query')) {
  /**
   * [build_url_query description]
   * @param  array   $params    [description]
   * @param  boolean $urlEncode [description]
   * @return string             [description]
   */
  function build_url_query(array $params, bool $urlEncode=true):?string {
    if ($params == null) return null;
    $queryString = '?';
    foreach($params as $key => $val) {
      if (is_array($val)) {
        foreach ($val as $value) {
          $queryString .= $key."=".($urlEncode ? rawurlencode($value) : $value)."&";
        }
        continue;
      }

      $queryString .= $key."=".($urlEncode ? rawurlencode($val) : $val)."&";
    }
    return substr($queryString, 0, strlen($queryString) - 1);
  }
}
