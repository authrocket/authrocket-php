<?php

namespace AuthRocket;

class AuthProviderTest extends TestCase {

  function setUp() {
    parent::setUp();
    $this->createRealm();
    $this->authProvider = $this->client->authProviders->first();
    $this->assertNotNull($this->authProvider);
  }


  function testAll() {
    $res = $this->client->authProviders->all();
    $this->assertNoError($res);
    $this->assertEquals(2, count($res->results));
    $this->assertEquals('auth_provider', $res->results[0]['object']);
  }

  function testFind() {
    $res = $this->client->authProviders->find($this->authProvider->id);
    $this->assertNoError($res);
    $this->assertEquals('auth_provider', $res->object);
  }

  function testCreate() {
    $res = $this->client->authProviders->create([
      'provider_type' =>'google',
      'client_id'     => 'pinky-and',
      'client_secret' => 'the-brain'
    ]);
    $this->assertNoError($res);
    $this->assertEquals('auth_provider', $res->object);
    $this->assertRegExp('/^ap_/', $res->id);
  }

  function testUpdate() {
    $this->assertEquals(8, $this->authProvider->min_length);
    $res = $this->client->authProviders->update($this->authProvider->id, ['min_length'=>12]);
    $this->assertNoError($res);
    $this->assertEquals(12, $res->min_length);
  }

  function testDelete() {
    $this->createAuthProvider();
    $res = $this->client->authProviders->all();
    $this->assertNoError($res);
    $count = count($res->results);

    $res = $this->client->authProviders->delete($this->authProvider->id);
    $this->assertNoError($res);

    $res = $this->client->authProviders->all();
    $this->assertNoError($res);
    $this->assertEquals($count-1, count($res->results));
  }

  function testAuthorizeUrls() {
    $this->createAuthProvider();
    $res = $this->client->authProviders->authorizeUrls([
      'redirect_uri' => 'https://local.dev/'
    ]);
    $this->assertNoError($res);
    $this->assertEquals(1, count($res->results));
    $this->assertEquals('facebook', $res->results[0]['provider_type']);
  }

  function testAuthorizeUrl() {
    $this->createAuthProvider();
    $res = $this->client->authProviders->authorizeUrl($this->authProvider->id, [
      'redirect_uri' => 'https://local.dev/'
    ]);
    $this->assertNoError($res);
    $this->assertEquals('facebook', $res->provider_type);
  }

  function testAuthorize() {
    if (!getenv('USE_DUMMY_AP'))
      $this->markTestSkipped();
    $this->createDummyAuthProvider();
    $res = $this->client->authProviders->authorizeUrl($this->authProvider->id, [
      'redirect_uri' => 'https://local.dev/'
    ]);

    $query = parse_url($res->auth_url, PHP_URL_QUERY);
    $pairs = preg_split('/&/', $query);
    $pairSet = [];
    foreach($pairs as $pair) {
      list($key, $val) = preg_split('/=/', $pair, 2);
      $pairSet[urldecode($key)] = urldecode($val);
    }
    $this->assertNotNull($pairSet['state']);

    $res = $this->client->authProviders->authorize([
      'code'  => 'invalid',
      'state' => $pairSet['state']
    ]);
    $this->assertMatchesError('/Error validating code/', $res);
    $this->assertRegExp('/^http/', $res->metadata['retry_url']);

    $res = $this->client->authProviders->authorize([
      'code'  => 'abcdefgh',
      'state' => $pairSet['state']
    ]);
    $this->assertNoError($res);
    $this->assertEquals('session', $res->object);
  }

  function testAuthorizeToken() {
    if (!getenv('USE_DUMMY_AP'))
      $this->markTestSkipped();
    $this->createDummyAuthProvider();
    $res = $this->client->authProviders->authorizeToken($this->authProvider->id, [
      'access_token' => 'abcdefgh'
    ]);
    $this->assertNoError($res);
    $this->assertEquals('session', $res->object);
  }

}

?>
