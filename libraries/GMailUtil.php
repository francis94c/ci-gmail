<?php
declare(strict_types=1);
defined('BASEPATH') OR exit('No direct script access allowed');

class GMailCURL {

  const GET  = 'GET';
  const POST = 'POST';

  private $method;
  private $userAgent;

  function __construct(string $method, string $userAgent='CodeIgniter GMail API') {
    $this->method = $method;
    $this->userAgent = $userAgent;
  }

  function __invoke(string $url, array $header=[], mixed $body=null):array {
    if ($body != null) $body = json_encode($body);

    $ch = curl_init($url);

    // Defaults.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    // Header.
    $header[] = 'Content-Type: application/json';
    $header[] = 'User-Agent: '.$this->userAgent;
    if ($body != null) $header[] = 'Content-Length: '.strlen($body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    // Request Method and Body.
    if ($this->method == self::POST) {
      curl_setopt($ch, CURLOPT_POST, true);
      if ($body != null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
      }
    }
    // Exec.
    $result = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$code, $result];
  }
}
