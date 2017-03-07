<?php

namespace AuthRocket;

class ClientTest extends TestCase {

  function setUp() {
    // skip default setUp()
  }

  function tearDown() {
    // skip default tearDown()
  }


  function testJwtSecret() {
    $client = new \AuthRocket\AuthRocket([
      'url'       => 'https://api-e1.authrocket.com/v1',
      'apiKey'    => 'ko_SAMPLE'
    ]);
    $this->assertEquals(null, $client->getDefaultJwtSecret());

    $client->setDefaultJwtSecret('test123');
    $this->assertEquals('test123', $client->getDefaultJwtSecret());

    $client = new \AuthRocket\AuthRocket([
      'url'       => 'https://api-e1.authrocket.com/v1',
      'apiKey'    => 'ko_SAMPLE'
    ]);
    $this->assertEquals(null, $client->getDefaultJwtSecret());

    $client = new \AuthRocket\AuthRocket([
      'url'       => 'https://api-e1.authrocket.com/v1',
      'apiKey'    => 'ko_SAMPLE',
      'jwtSecret' => 'jsk_SAMPLE'
    ]);
    $this->assertEquals('jsk_SAMPLE', $client->getDefaultJwtSecret());
  }

}

?>
