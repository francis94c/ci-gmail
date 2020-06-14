<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('base64url_decode')) {
  /**
   * [base64url_decode decode base64 url encoded data.]
   *
   * @param  string      $data   Data to decode.
   *
   * @param  boolean     $strict Strinct flag.
   *
   * @return string|bool          Decoded String.
   */
  function base64url_decode(string $data, bool $strict=false) {
    $b64 = strtr($data, '-_', '+/');
    return base64_decode($b64, $strict);
  }
}
