<?php
declare(strict_types=1);
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('GMailScopes.php');
require_once('GMailUtil.php');

class GMail {

  const AUTH_URL  = 'https://accounts.google.com/o/oauth2/auth';
  const TOKEN_URL = 'https://accounts.google.com/o/oauth2/token';
  const API       = 'https://www.googleapis.com/gmail/v1/users/';
  const HTTP_CODE = 'http_code';
  private $clientId;
  private $clientSecret;
  private $redirectUri = 'urn:ietf:wg:oauth:2.0:oob';
  private $token;

  function __construct($params=null) {
    get_instance()->load->splint('francis94c/ci-gmail', '%curl');
    if ($params != null) $this->init($params);
  }

  /**
   * [init Initialize library with cofigs. Can be called multiple times to set
   *       config items]
   * @param array $config Associative Config Array.
   */
  public function init(array $config):void {
    $this->clientId = $config['client_id'] ?? $this->clientId;
    $this->clientSecret = $config['client_secret'] ?? $this->clientSecret;
    $this->redirectUri = $config['redirect_uri'] ?? $this->redirectUri;
  }
  /**
   * [setAuthorizationToken description]
   * @param string $token [description]
   */
  public function setAuthorizationToken(string $token):void {
    $this->token = $token;
  }
  /**
   * [getClientId Get Client ID.]
   * @return null|string Client ID.
   */
  public function getClientId():?string {
    return $this->clientId;
  }
  /**
   * [getAuthorizeUrl Gets/composes the authorize url to direct users to so they
   *                  can give your application access to their GMail accounts
   *                  based on the given scopes.]
   * @param  string $scope        Access Scope.
   * @param  string $redirectUri  URL to redirect to after access is granted.
   * @param  string $responseType Response type. 'code' by default.
   * @param  bool   $prompt       Add the prompt=consent query to the URL.
   * @return string               Authorize URL
   */
  public function getAuthorizeUrl(string $scope, string $redirectUri=null, string $responseType='code', string $accessType='offline', bool $prompt=false):string {
    $redirectUri = $redirectUri ?? $this->redirectUri;
    if ($scope == null) throw new Exception("GMail scope cannot be null");
    $params = [
      'client_id'     => $this->clientId,
      'redirect_uri'  => $redirectUri,
      'scope'         => $scope,
      'response_type' => $responseType,
      'access_type'   => $accessType
    ];
    if ($prompt) $params['prompt'] = 'consent';
    return self::AUTH_URL . build_url_query($params, false);
  }
  /**
   * [getToken description]
   * @param  string $code [description]
   * @return [type]       [description]
   */
  public function getToken(string $code, string $redirectUri=null):?array {
    $redirectUri = $redirectUri ?? $this->redirectUri;
    list($code, $response) = (new GMailCURL(GMailCURL::POST))(
      self::TOKEN_URL . build_url_query([
        'code'          => $code,
        'client_id'     => $this->clientId,
        'client_secret' => $this->clientSecret,
        'redirect_uri'  => $redirectUri,
        'grant_type'    => 'authorization_code'
      ], false)
    );
    if ($response !== false)$this->process_response($code, $response);
    return null;
  }
  /**
   * [getProfile description]
   * @param  string $user [description]
   * @return [type]       [description]
   */
  public function getProfile(string $user='me'):?array {
    list($code, $response) = (new GMailCURL(GMailCURL::GET))(
      self::API . "$user/profile",
      ["Authorization: Bearer $this->token"]
    );
    if ($response !== false) return $this->process_response($code, $response);
    return null;
  }
  /**
   * [process_response description]
   * @param  int    $code   [description]
   * @param  string $response [description]
   * @return [type]         [description]
   */
  private function process_response(int $code, string $response) {
    $response = json_decode($response, true);
    $response[self::HTTP_CODE] = $code;
    return $response;
  }
}
?>
