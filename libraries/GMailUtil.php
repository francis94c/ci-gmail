<?php
declare(strict_types=1);
defined('BASEPATH') OR exit('No direct script access allowed');

class URLQueryBuilder {

  /**
   * [private description]
   * @var [type]
   */
  private $query = [];

  function __construct(array $query=null) {
    if ($query != null) {
      foreach ($query as $key => $val) {
        $this->query[$key] = $val;
      }
    }
  }
  /**
   * [set description]
   * @param string $key [description]
   * @param string $val [description]
   */
  public function set(string $key, string $val):void {
    $this->query[$key] = $val;
  }
  /**
   * [build description]
   * @return [type] [description]
   */
  public function build():string {
    $queryString = '?';
    foreach($this->query as $key => $val) {
      $queryString += $key."=".rawurlencode($val)."&";
    }
    return substr($queryString, 0, strlen($queryString) - 1);
  }
}
