<?php
declare(strict_types=1);
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('GMailScopes.php');
require_once('GMailUtil.php');

class GMail {

  private const AUTH_URL = 'https://accounts.google.com/o/oauth2/auth';
  private $ci;
  private $clientId;

  /**
   * [init description]
   * @param [type] $config [description]
   */
  public function init(array $config=null):void {
    $this->clientId = $config['client_id'] ?? $this->clientId;
  }
}
?>
