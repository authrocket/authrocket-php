<?php

namespace AuthRocket;

class SessionTest extends TestCase {

  function setUp() {
    parent::setUp();
    $this->createSession();
  }

  function tearDown() {
    $this->client->setDefaultJwtKey(null);
    parent::tearDown();
  }


  function testAll() {
    $res = $this->client->sessions->all(['user_id'=>$this->user->id]);
    $this->assertNoError($res);
    $this->assertEquals(1, count($res->results));
    $this->assertEquals('session', $res->results[0]['object']);
  }

  function testFind() {
    $res = $this->client->sessions->find($this->session->id);
    $this->assertNoError($res);
    $this->assertEquals('session', $res->object);
  }

  /**
   * @expectedException AuthRocket\Error
   */
  function testFromTokenMissingKey() {
    $this->client->setDefaultJwtKey(null);
    $this->client->setLoginrocketUrl(null);
    $this->client->sessions->fromToken($this->session->token);
  }

  function testFromTokenHs256() {
    $this->realm = $this->client->realms->update($this->realm->id, [
      'jwt_algo'=>'hs256',
      'jwt_scopes'=>'ar.orgs'
    ]);
    $this->assertNoError($this->realm);
    $this->createSession();

    $this->client->setDefaultJwtKey('wrong-key');
    $res = $this->client->sessions->fromToken($this->session->token);
    $this->assertNull($res);

    $this->assertRegExp('/^jsk_/', $this->realm->jwt_key);
    $this->client->setDefaultJwtKey($this->realm->jwt_key);
    $res = $this->client->sessions->fromToken('blahblah');
    $this->assertNull($res);

    $res = $this->client->sessions->fromToken($this->session->token);
    $this->assertInstanceOf('\AuthRocket\Response', $res);
    $this->assertEquals('session', $res->object);
    $this->assertEquals('user', $res->user['object']);
    $this->assertEquals('membership', $res->user['memberships'][0]['object']);
    $this->assertEquals('org', $res->user['memberships'][0]['org']['object']);
  }

  function testFromTokenRs256() {
    $this->assertRegExp('/PUBLIC KEY/', $this->realm->jwt_key);
    $this->client->setDefaultJwtKey($this->realm->jwt_key);
    $res = $this->client->sessions->fromToken('blahblah');
    $this->assertNull($res);

    $res = $this->client->sessions->fromToken($this->session->token);
    $this->assertInstanceOf('\AuthRocket\Response', $res);
    $this->assertEquals('session', $res->object);
    $this->assertEquals('user', $res->user['object']);

    $shortKey = preg_replace(['/-{5}(BEGIN|END) PUBLIC KEY-{5}/', '/\n/'], '', $this->realm->jwt_key);
    $this->client->setDefaultJwtKey($shortKey);
    $res = $this->client->sessions->fromToken($this->session->token);
    $this->assertInstanceOf('\AuthRocket\Response', $res);
    $this->assertEquals('session', $res->object);
    $this->assertEquals('user', $res->user['object']);
  }

  function testFromTokenDynamic() {
    $this->createClientApp();
    $this->createDomain();
    $lrUrl = new \GuzzleHttp\Psr7\Uri($this->client->getLoginrocketUrl());
    $lrUrl = $lrUrl->withHost($this->domain->fqdn);
    $this->client->setLoginrocketUrl($lrUrl);

    $res = $this->client->sessions->fromToken('blahblah');
    $this->assertNull($res);

    $res = $this->client->sessions->fromToken($this->session->token);
    $this->assertEquals(1, count(Loginrocket::$jwkSet), 'jwtSet should be populated');
    $this->assertInstanceOf('\AuthRocket\Response', $res);
    $this->assertEquals('session', $res->object);
    $this->assertEquals('user', $res->user['object']);
  }

  function testCreate() {
    $res = $this->client->sessions->create(['user_id'=>$this->user->id]);
    $this->assertNoError($res);
    $this->assertEquals('session', $res->object);
    $this->assertTrue(is_string($res->token));
  }

  function testDelete() {
    $res = $this->client->sessions->delete($this->session->id);
    $this->assertNoError($res);
  }

}

?>
