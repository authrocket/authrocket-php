<?php

namespace AuthRocket;

class RealmTest extends TestCase {

  function setUp(): void {
    // parent::setUp();
    $this->client = self::buildClient();
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

  function testInvalidFind() {
    $this->expectException(\AuthRocket\RecordNotFound::class);
    $res = $this->client->realms->find('rl_invalid');
  }

  function testCreate() {
    $res = $this->client->realms->create(['name'=>'AR-php hello '.rand()]);
    $this->assertNoError($res);
    $this->assertEquals('realm', $res->object);
    $this->assertMatchesRegularExpression('/^rl_/', $res->id);
  }

  function testUpdate() {
    $newName = 'AR-php new name '.rand();
    $this->assertFalse($newName == $this->realm->name);
    $res = $this->client->realms->update($this->realm->id, ['name'=>$newName]);
    $this->assertNoError($res);
    $this->assertEquals($newName, $res->name);
  }

  function testDelete() {
    $id = $this->realm->id;
    $res = $this->client->realms->delete($id);
    $this->assertNoError($res);
    $this->realm = null;
    $this->expectException(\AuthRocket\RecordNotFound::class);
    $res = $this->client->realms->find($id, ['state'=>'active']);
  }

  function testReset() {
    $this->client->setDefaultRealm($this->realm->id);
    $this->createOrg();
    $res = $this->client->orgs->all();
    $this->assertNoError($res);
    $this->assertEquals(1, count($res->results));

    $res = $this->client->realms->reset($this->realm->id);
    $this->assertNoError($res);
  }

}

?>
