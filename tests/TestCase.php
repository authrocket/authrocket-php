<?php

use PHPUnit\Framework\TestCase;

namespace AuthRocket;

class TestCase extends \PHPUnit\Framework\TestCase {

  static function setUpBeforeClass(): void {
    error_reporting(E_ALL);
  }

  static function tearDownAfterClass(): void {
    self::deleteStaleRealms();
  }

  function setUp(): void {
    $this->client = self::buildClient();
    $this->createRealm();
    $this->client->setDefaultRealm($this->realm->id);
  }

  function tearDown(): void {
    $this->deleteRealm();
  }


  function assertNoError($response) {
    $this->assertTrue(is_array($response->errors));
    $this->assertEquals(0, count($response->errors), "Unexpected error(s): ".$response->errorMessages());
  }

  function assertMatchesError($regexp, $response) {
    $this->assertMatchesRegularExpression($regexp, $response->errorMessages());
  }




  protected $client;

  protected static function buildClient() {
    $cl = AuthRocket::autoConfigure();
    // $cl->debug = true;
    return $cl;
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

  protected function activateTotpAuthProvider() {
    $this->authProvider =
      $this->client->authProviders->find('totp', [
        'realm_id' => $this->realm->id
      ]);
    $this->authProvider =
      $this->client->authProviders->update($this->authProvider->id, [
        'state' => 'active'
       ]);
    $this->assertNoError($this->authProvider);
    $this->assertEquals('auth_provider', $this->authProvider->object);
  }


  protected function createClientApp() {
    $this->clientApp =
      $this->client->clientApps->create([
        'client_type'   => 'standard',
        'name'          => 'ar-php-sdk',
        'redirect_uris' => ['http://localhost:3000/']
      ]);
    $this->assertNoError($this->clientApp);
    $this->assertEquals('client_app', $this->clientApp->object);
  }


  protected function createDomain() {
    $this->domain =
      $this->client->domains->create([
        'domain_type' => 'loginrocket'
      ]);
    $this->assertNoError($this->domain);
    $this->assertEquals('domain', $this->domain->object);
  }


  protected function createInvitation() {
    $em = 'invitee-'.rand(1,99999).'@example.com';
    $this->invitation =
      $this->client->invitations->create([
        'email'           => $em,
        'invitation_type' => 'request'
      ]);
    $this->assertNoError($this->invitation);
    $this->assertEquals('invitation', $this->invitation->object);
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

  protected static function deleteStaleRealms() {
    $client = self::buildClient();
    $res = $client->realms->all();
    foreach($res->results as $r) {
      if (preg_match('/^AR-php /', $r['name'])) {
        $client->realms->delete($r['id']);
      }
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
        'email'      => $em,
        'password'   => 'quick-fox-jumped-over-the-moon',
        'first_name' => 'george'
      ]);
    $this->assertNoError($this->user);
    $this->assertEquals('user', $this->user->object);
  }

}

?>
