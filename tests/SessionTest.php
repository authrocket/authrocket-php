<?php

namespace AuthRocket;

class SessionTest extends TestCase {

  function setUp() {
    parent::setUp();
    $this->createSession();
  }

  function tearDown() {
    $this->client->setDefaultJwtSecret(null);
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

  function testFromTokenHs256() {
    $this->assertTrue(is_string($this->realm->jwt_key));
    $res = $this->client->sessions->fromToken('blahblah', ['jwtSecret'=>$this->realm->jwt_key]);
    $this->assertNull($res);

    $res = $this->client->sessions->fromToken($this->session->token, ['jwtSecret'=>$this->realm->jwt_key]);
    $this->assertInstanceOf('\AuthRocket\Response', $res);
    $this->assertEquals('session', $res->object);
    $this->assertEquals('user', $res->user['object']);
  }

  function testFromTokenRs256() {
    $this->realm = $this->client->realms->update($this->realm->id, ['jwt_algo'=>'rs256']);
    $this->assertNoError($this->realm);
    $this->createSession();

    $this->assertRegExp('/PUBLIC KEY/', $this->realm->jwt_key);
    $res = $this->client->sessions->fromToken('blahblah', ['jwtSecret'=>$this->realm->jwt_key]);
    $this->assertNull($res);

    $res = $this->client->sessions->fromToken($this->session->token, ['jwtSecret'=>$this->realm->jwt_key]);
    $this->assertInstanceOf('\AuthRocket\Response', $res);
    $this->assertEquals('session', $res->object);
    $this->assertEquals('user', $res->user['object']);
  }

  function testFromTokenDefaultJwt() {
    $this->client->setDefaultJwtSecret($this->realm->jwt_key);
    $res = $this->client->sessions->fromToken($this->session->token);
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
