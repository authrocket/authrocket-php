<?php

use PHPUnit\Framework\TestCase;

namespace AuthRocket;

class TestCase extends \PHPUnit_Framework_TestCase {

  static function setUpBeforeClass() {
    error_reporting(E_ALL);
    self::buildClient();
  }

  function setUp() {
    $this->client = self::$ar_client;
    $this->createRealm();
    $this->client->setDefaultRealm($this->realm->id);
  }

  function tearDown() {
    $this->deleteRealm();
  }


  function assertNoError($response) {
    $this->assertTrue(is_array($response->errors));
    $this->assertEquals(0, count($response->errors), "Unexpected error(s): ".$response->errorMessages());
  }

  function assertMatchesError($regexp, $response) {
    $this->assertRegExp($regexp, $response->errorMessages());
  }




  protected static $ar_client;
  protected $client;

  protected static function buildClient() {
    self::$ar_client = AuthRocket::autoConfigure();
    // self::$ar_client->debug = true;
  }


  protected function createAuthProvider() {
    $this->authProvider =
      $this->client->authProviders->create([
        'provider_type' => 'facebook',
        'client_id'     => 'dummy-1',
        'client_secret' => 'dummy-2'
      ]);
    $this->assertNoError($this->authProvider);
    $this->assertEquals('auth_provider', $this->authProvider->object);
  }

  protected function createDummyAuthProvider() {
    $this->authProvider =
      $this->client->authProviders->create([
        'provider_type' => getenv('USE_DUMMY_AP'),
        'client_id'     => 'dummy-1',
        'client_secret' => 'dummy-2'
      ]);
    $this->assertNoError($this->authProvider);
    $this->assertEquals('auth_provider', $this->authProvider->object);
  }


  protected function createMembership() {
    if (!isset($this->user))
      $this->createUser();
    if (!isset($this->org))
      $this->createOrg();
    $this->membership =
      $this->client->memberships->create([
        'user_id' => $this->user->id,
        'org_id'  => $this->org->id
      ]);
    $this->assertNoError($this->membership);
    $this->assertEquals('membership', $this->membership->object);
  }

 
   protected function createOrg() {
    $this->org =
      $this->client->orgs->create([
        'name' => 'default1'
      ]);
    $this->assertNoError($this->org);
    $this->assertEquals('org', $this->org->object);
  }


  protected function createRealm() {
    if (isset($this->realm))
      return;
    $this->realm = 
      $this->client->realms->create([
        'name' => 'AR-php '.time().'-'.rand(1,99999),
      ]);
    if (!preg_match('/^rl_/', $this->realm->id)) {
      die('TestCase::createRealm() did not return a valid realm ID');
    }
    $this->assertNoError($this->realm);
    $this->assertEquals('realm', $this->realm->object);
  }

  protected function deleteRealm() {
    if (isset($this->realm)) {
      $res = $this->client->realms->delete($this->realm->id);
      $this->assertNoError($res);
    }
  }


  protected function createSession() {
    if (!isset($this->membership))
      $this->createMembership();
    $this->session = 
      $this->client->sessions->create([
        'user_id' => $this->user->id
      ]);
    $this->assertNoError($this->session);
    $this->assertEquals('session', $this->session->object);
  }


  protected function createUser() {
    $em = 'user-'.rand(1,99999).'@example.com';
    $this->user =
      $this->client->users->create([
        'user_type'  => 'human',
        'username'   => $em,
        'email'      => $em,
        'password'   => 'quick-fox-jumped-over-the-moon',
        'first_name' => 'george'
      ]);
    $this->assertNoError($this->user);
    $this->assertEquals('user', $this->user->object);
  }

}

?>
