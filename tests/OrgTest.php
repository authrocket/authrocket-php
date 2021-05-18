<?php

namespace AuthRocket;

class OrgTest extends TestCase {

  function setUp() {
    parent::setUp();
    $this->createOrg();
  }


  function testAll() {
    $this->createOrg();
    $res = $this->client->orgs->all();
    $this->assertNoError($res);
    $this->assertEquals(2, count($res->results));
    $this->assertEquals('org', $res->results[0]['object']);
  }

  function testFind() {
    $res = $this->client->orgs->find($this->org->id);
    $this->assertNoError($res);
    $this->assertEquals('org', $res->object);
  }

  function testCreate() {
    $res = $this->client->orgs->create(['name'=>'hello']);
    $this->assertNoError($res);
    $this->assertEquals('org', $res->object);
    $this->assertRegExp('/^org_/', $res->id);
  }

  function testUpdate() {
    $this->assertEquals('default1', $this->org->name);
    $res = $this->client->orgs->update($this->org->id, ['name'=>'new name']);
    $this->assertNoError($res);
    $this->assertEquals('new name', $res->name);
  }

  function testDeleteAsClose() {
    $res = $this->client->orgs->delete($this->org->id);
    $this->assertNoError($res);
    $this->assertEquals('closed', $res->state);
  }

  function testDeleteAsDelete() {
    $res = $this->client->orgs->delete($this->org->id, ['force'=>true]);
    $this->assertNoError($res);
    $res = $this->client->orgs->all();
    $this->assertNoError($res);
    $this->assertEquals(0, count($res->results));
  }

}

?>
