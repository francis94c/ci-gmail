<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message
{
  /**
   * [protected description]
   * @var [type]
   */
  protected $message;

  public function __construct($jsonString)
  {
    $this->message = json_decode($jsonString);
  }
  /**
   * [header description]
   * @date   2019-11-22
   * @param  string     $key [description]
   * @return string          [description]
   */
  public function header(string $key):string
  {
    foreach ($this->message->payload->headers as $header) {
      if ($header->name == $key) {
        return $header->value;
      }
    }
  }
  /**
   * [headers description]
   * @date   2019-11-22
   * @return array      [description]
   */
  public function headers():array
  {
    return $this->message->payload->headers;
  }
  /**
   * [body description]
   * @date   2019-11-22
   * @return string     [description]
   */
  public function body():?string
  {
    if (isset($this->message->payload->body->data)) {
      return base64url_decode($this->message->payload->body->data);
    }
    return null;
  }
}
