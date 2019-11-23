<?php
declare(strict_types=1);
defined('BASEPATH') OR exit('No direct script access allowed');

class GMail {

  const AUTH_URL  = 'https://accounts.google.com/o/oauth2/auth';
  const TOKEN_URL = 'https://www.googleapis.com/oauth2/v4/token';
  const API       = 'https://www.googleapis.com/gmail/v1/users/';
  const HTTP_CODE = 'http_code';
  const PACKAGE   = 'francis94c/ci-gmail';
  private $clientId;
  private $clientSecret;
  private $redirectUri = 'urn:ietf:wg:oauth:2.0:oob';
  private $token;
  private $userAgent = 'CodeIgniter GMail API';

  function __construct($params=null)
  {
    get_instance()->load->splint('francis94c/ci-gmail', '%curl');
    get_instance()->load->splint('francis94c/ci-gmail', '%base64');

    if ($params != null) $this->init($params);

    spl_autoload_register(function($name) {
      $oldPath = set_include_path(APPPATH . 'splints/' . self::PACKAGE . '/libraries');
      require("$name.php");
      set_include_path($oldPath);
    });
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
   * [setAccessToken description]
   * @param string $token [description]
   */
  public function setAccessToken(string $token):void {
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
  public function getAuthorizeUrl(string $scope, string $redirectUri=null, string $responseType='code', string $accessType='offline', bool $prompt=false):string
  {
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
  public function getToken(string $code, string $redirectUri=null):?array
  {
    $redirectUri = $redirectUri ?? $this->redirectUri;
    $ch = curl_init(self::TOKEN_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    if (ENVIRONMENT == 'development') {
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }
    $body = http_build_query([
      'code'          => $code,
      'client_id'     => $this->clientId,
      'client_secret' => $this->clientSecret,
      'redirect_uri'  => $this->redirectUri,
      'grant_type'    => 'authorization_code'
    ]);
    $header = [
      'Content-Type: application/x-www-form-urlencoded',
      'Content-Length: ' . strlen($body)
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
    // Request Method and Body.
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($response !== false) return $this->process_response($code, $response);
    return null;
  }
  /**
   * [refreshAccessToken description]
   * @param  string $refreshToken [description]
   * @return [type]               [description]
   */
  public function refreshAccessToken(string $refreshToken):?array {
    $ch = curl_init(self::TOKEN_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    if (ENVIRONMENT == 'development') {
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }
    $body = http_build_query([
      'refresh_token' => $refreshToken,
      'client_id'     => $this->clientId,
      'client_secret' => $this->clientSecret,
      'grant_type'    => 'refresh_token'
    ]);
    $header = [
      'Content-Type: application/x-www-form-urlencoded',
      'Content-Length: ' . strlen($body)
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
    // Request Method and Body.
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    // Exec.
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($response !== false) return $this->process_response($code, $response);
    return null;
  }
  /**
   * [getProfile Get user's profile
   * see https://developers.google.com/gmail/api/v1/reference/users/getProfile]
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
   * [watch Causes push notifications to be sent on events which occur in the
   * user's mailbox. Requires Google Cloud PubSub.
   * see https://developers.google.com/gmail/api/v1/reference/users/watch]
   * @param  string $topic             Topic to push events to.
   * @param  mixed  $labelIds          Narrow down labels in the mailbox, whose
   *                                   mailbox event, are being listened to.
   *                                   events are to be listened to.
   * @param  string $userId            The ID/Email address of the user.
   * @param  string $labelFilterAction [description]
   * @return [type]                    [description]
   */
  public function watch(string $topic, $labelIds=null, string $userId='me', string $labelFilterAction='include'):?array {
    $body = [
      'topicName'         => $topic,
      'labelFilterAction' => $labelFilterAction
    ];

    if ($labelIds != null) {
      if (is_scalar($labelIds)) {
        $body['labelIds'] = [$labelIds];
      } elseif (is_array($labelIds)) {
        $body['labelIds'] = $labelIds;
      }
    }

    list($code, $response) = (new GMailCURL(GMailCURL::POST))(
      self::API . "$userId/watch",
      ["Authorization: Bearer $this->token"],
      $body
    );
    if ($response !== false) return $this->process_response($code, $response);
    return null;
  }
  /**
   * [endWatch stop watch operations on given email ID]
   * @date   2019-11-20
   * @param  string     $userId ID or Email Address of the user.
   * @return bool               [description]
   */
  public function endWatch(string $userId='me'):bool
  {
    list($code, $response) = (new GMailCURL(GMailCURL::POST))(
      self::API . "$userId/stop",
      ["Authorization: Bearer $this->token"]
    );
    if ($response !== false) return $code == 204;
    return false;
  }
  /**
   * [getLabels description]
   * @date   2019-11-20
   * @param  string     $userID [description]
   * @return null|array         [description]
   */
  public function getLabels(string $userId='me'):?array
  {
    list($code, $response) = (new GMailCURL(GMailCURL::GET))(
      self::API . "$userId/labels",
      ["Authorization: Bearer $this->token"]
    );
    if ($response !== false) {
      return json_decode($response)->labels;
    }
    return null;
  }
  /**
   * [getMessages description]
   * @date   2019-11-21
   * @param  string     $userId           [description]
   * @param  [type]     $labelIds         [description]
   * @param  [type]     $q                [description]
   * @param  [type]     $maxMessages      [description]
   * @param  [type]     $pageToken        [description]
   * @param  boolean    $includeSpamTrash [description]
   * @param  [type]     $truncateAfter    [description]
   * @return [type]                       [description]
   */
  public function getMessages(string $userId='me', array $labelIds=null,
  string $q=null, int $maxMessages=null, string $pageToken=null, bool $includeSpamTrash=false,
  $truncateAfter=null):?object
  {
    $query = [];

    if ($labelIds != null) $query['labelIds'] = $labelIds;
    if ($includeSpamTrash) $query['includeSpamTrash'] = $includeSpamTrash;
    if ($q != null) $query['q'] = $q;
    if ($pageToken != null) $query['pageToken'] = $pageToken;
    if ($maxMessages != null) $query['maxResults'] = $maxMessages;

    list($code, $response) = (new GMailCURL(GMailCURL::GET))(
      self::API . "$userId/messages?" . http_build_query($query),
      ["Authorization: Bearer $this->token"]
    );

    if ($response !== false) {
      if ($truncateAfter != null && $code == 200) {
        $response = json_decode($response);
        $response->messages = array_filter($response->messages, function ($e) use ($truncateAfter) {
          return strcmp($truncateAfter, $e->id) <= 0;
        });
        $response->{self::HTTP_CODE} = $code;
        return $response;
      }

      return $this->process_response($code, $response);
    }

    return null;
  }
  /**
   * [getMessage description]
   * @date   2019-11-21
   * @param  string       $userId          [description]
   * @param  string       $messageId       [description]
   * @param  string       $format          [description]
   * @param  [type]       $metadataHeaders [description]
   * @return Message|null                  [description]
   */
  public function getMessage(string $userId='me', string $messageId,
  string $format='full', array $metadataHeaders=null):?Message
  {
    $query = [];

    if ($format != 'full' && $format != null) $query['format'] = $format;
    if ($metadataHeaders != null) $query['metadataHeaders'] = $metadataHeaders;

    list($code, $response) = (new GMailCURL(GMailCURL::GET))(
      self::API . "$userId/messages/$messageId?" . http_build_query($query),
      ["Authorization: Bearer $this->token"]
    );

    if ($response !== false) return new Message($response);

    return null;
  }
  /**
   * [process_response description]
   * @param  int    $code   [description]
   * @param  string $response [description]
   * @return [type]         [description]
   */
  private function process_response(int $code, string $response) {
    $response = json_decode($response);
    $response->{self::HTTP_CODE} = $code;
    return $response;
  }
}
?>
