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
   * [__get description]
   * @date   2019-11-23
   * @param  string     $key [description]
   * @return [type]          [description]
   */
  public function __get(string $key):string
  {
    return $this->message->{$key};
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
   * [isMultiPart description]
   * @date   2019-11-23
   * @return bool       [description]
   */
  public function isMultiPart():bool
  {
    return isset($this->message->payload->parts);
  }
  /**
   * [getSize description]
   * @date   2019-11-23
   * @return int        [description]
   */
  public function getSize():int
  {
    return $this->message->payload->body->size;
  }
  /**
   * [body description]
   * @date   2019-11-23
   * @param  integer    $partId [description]
   * @return [type]             [description]
   */
  public function body(int $partId=0)
  {
    if ($this->isMultiPart()) {
      return new MessagePart($this->message->payload->parts[$partId]);
    }

    return base64url_decode($this->message->payload->body->data);
  }
}
