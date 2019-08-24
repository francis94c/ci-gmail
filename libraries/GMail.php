<?php
declare(strict_types=1);
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('GMailScopes.php');
require_once('GMailUtil.php');

class GMail {

  const AUTH_URL = 'https://accounts.google.com/o/oauth2/auth';
  private $ci;
  private $clientId;

  /**
   * [init Initialize library with cofigs. Can be called multiple times to set
   *       config items]
   * @param array $config Associative Config Array.
   */
  public function init(array $config=null):void {
    $this->clientId = $config['client_id'] ?? $this->clientId;
  }
  /**
   * [getAuthorizeUrl Gets/composes the authorize url to direct users to so they
   *                  can give your application access to their GMail accounts
   *                  based on the given scopes.]
   * @param  string $scope        Access Scope.
   * @param  string $redirectUri  URL to redirect to after access is granted.
   * @param  string $responseType Response type. 'code' by default.
   * @return string               Authorize URL
   */
  public function getAuthorizeUrl(string $scope, string $redirectUri='urn:ietf:wg:oauth:2.0:oob', string $responseType='code', string $accessType='offline'):string {
    if ($scope == null) throw new Exception("GMail scope cannot be null");
    return self::AUTH_URL."?client_id=$this->clientId&redirect_uri=$redirectUri&scope=$scope&response_type=$responseType&access_type=$accessType";
  }
  /**
   * [getClientId Get Client ID.]
   * @return null|string Client ID.
   */
  public function getClientId():?string {
    return $this->clientId;
  }
}
?>
