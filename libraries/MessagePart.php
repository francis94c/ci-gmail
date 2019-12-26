<?php
declare(strict_types=1);

defined('BASEPATH') OR exit('No direct script access allowed');

class MessagePart
{
  /**
   * [protected description]
   * @var [type]
   */
  protected $part;
  /**
   * [__construct description]
   * @date  2019-11-23
   * @param [type]     $part [description]
   */
  function __construct($part)
  {
    $this->part = $part;
  }
  /**
   * [__get description]
   * @date   2019-11-23
   * @param  string     $key [description]
   * @return [type]          [description]
   */
  public function __get(string $key):string
  {
    return $this->part->{$key};
  }
  /**
   * [header description]
   * @date   2019-11-22
   * @param  string      $key [description]
   * @return string|null      [description]
   */
  public function header(string $key):?string
  {
    foreach ($this->part->headers as $header) {
      if ($header->name == $key) {
        return $header->value;
      }
    }
    return null;
  }
  /**
   * [getSize description]
   * @date   2019-11-23
   * @return int        [description]
   */
  public function getSize():int
  {
    return $this->part->body->size;
  }
  /**
   * [body description]
   * @date   2019-11-23
   * @param  integer    $partId [description]
   * @return [type]             [description]
   */
  public function body(int $partId=0)
  {
    return base64url_decode($this->part->body->data);
  }
}
?>
