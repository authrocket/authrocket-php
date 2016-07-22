<?php

namespace AuthRocket;

class SessionTest extends TestCase {

  function setUp() {
    parent::setUp();
    $this->createSession();
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

  function testFromToken() {
    $this->assertTrue(is_string($this->realm->jwt_secret));
    $res = $this->client->sessions->fromToken('blahblah', ['jwtSecret'=>$this->realm->jwt_secret]);
    $this->assertNull($res);

    $res = $this->client->sessions->fromToken($this->session->token, ['jwtSecret'=>$this->realm->jwt_secret]);
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
