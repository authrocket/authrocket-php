<?php

namespace AuthRocket;

class ClientTest extends TestCase {

  function setUp() {
    // skip default setUp()
  }

  function tearDown() {
    // skip default tearDown()
  }


  function testJwtKey() {
    $client = new \AuthRocket\AuthRocket([
      'url'    => 'https://api-e2.authrocket.com/v2',
      'apiKey' => 'ks_SAMPLE'
    ]);
    $this->assertEquals(null, $client->getDefaultJwtKey());

    $client->setDefaultJwtKey('test123');
    $this->assertEquals('test123', $client->getDefaultJwtKey());

    $client = new \AuthRocket\AuthRocket([
      'url'    => 'https://api-e2.authrocket.com/v2',
      'apiKey' => 'ks_SAMPLE',
      'jwtKey' => 'jsk_SAMPLE'
    ]);
    $this->assertEquals('jsk_SAMPLE', $client->getDefaultJwtKey());
  }

  /**
   * @expectedException AuthRocket\Error
   */
  function testLoginrocketUrlEmpty() {
    $client = new \AuthRocket\AuthRocket([]);
    $client->getLoginrocketUrl();
  }

  function testLoginrocketUrl() {
    $client = new \AuthRocket\AuthRocket([
      'loginrocketUrl' => 'http://from.config/'
    ]);
    $this->assertEquals('http://from.config/', $client->getLoginrocketUrl());
  }

}

?>
