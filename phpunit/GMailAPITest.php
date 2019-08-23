<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;

class GMailAPITest extends TestCase {
  /**
   * Code Igniter Instance.
   * @var object
   */
  private static $ci;
  /**
   * Package name for simplicity
   * @var string
   */
  private const PACKAGE = "francis94c/ci-gmail";

  /**
   * Prerquisites for the Unit Tests.
   *
   * @covers JWT::__construct
   */
  public static function setUpBeforeClass(): void {
    self::$ci =& get_instance();
    $config = [
      'client_id' => 'abcde',
    ];
    self::$ci->load->package(self::PACKAGE);
    self::$ci->gmail->init($config);
  }
  /**
   * [testAuthorizeURL description]
   */
  public function testAuthorizeURL():void {
    $this->assertEquals(
      GMail::AUTH_URL.'?client_id=abcde&redirect_uri=https://example.com&scope='.GMailScopes::LABELS.'&response_type=code',
      self::$ci->gmail->getAuthorizeUrl(GMailScopes::LABELS, 'https://example.com')
    );
  }
}
