<?php

namespace AuthRocket;

class RealmTest extends TestCase {

  function setUp() {
    // parent::setUp();
    $this->client = self::$ar_client;
    $this->createRealm();
  }


  function testAll() {
    $res = $this->client->realms->all();
    $this->assertNoError($res);
    $this->assertGreaterThanOrEqual(1, count($res->results));
    $this->assertGreaterThanOrEqual(1, count($res->fields));
    $this->assertEquals('realm', $res->results[0]['object']);
    $this->assertEquals('realm', $res->fields[0]['object']);
  }

  function testFind() {
    $res = $this->client->realms->find($this->realm->id);
    $this->assertNoError($res);
    $this->assertEquals('realm', $res->object);
    $this->assertEquals('realm', $res->results['object']);
    $this->assertEquals('realm', $res->fields['object']);
    $this->assertNull($res->made_up_field);
  }

  function testCreate() {
    $res = $this->client->realms->create(['name'=>'hello '.rand()]);
    $this->assertNoError($res);
    $this->assertEquals('realm', $res->object);
    $this->assertRegExp('/^rl_/', $res->id);
  }

  function testUpdate() {
    $this->assertFalse('new name' == $this->realm->name);
    $res = $this->client->realms->update($this->realm->id, ['name'=>'new name']);
    $this->assertNoError($res);
    $this->assertEquals('new name', $res->name);
  }

  /**
   * @expectedException AuthRocket\RecordNotFound
   */
  function testDelete() {
    $res = $this->client->realms->delete($this->realm->id);
    $this->assertNoError($res);
    $res = $this->client->realms->find($this->realm->id, ['state'=>'active']);
  }

  function testReset() {
    $this->client->setDefaultRealm($this->realm->id);
    $this->createOrg();
    $res = $this->client->orgs->all();
    $this->assertNoError($res);
    $this->assertEquals(1, count($res->results));

    $res = $this->client->realms->reset($this->realm->id);
    $this->assertNoError($res);
    $res = $this->client->orgs->all();
    $this->assertNoError($res);
    $this->assertEquals(0, count($res->results));
  }

}

?>
