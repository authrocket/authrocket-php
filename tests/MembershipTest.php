<?php

namespace AuthRocket;

class MembershipTest extends TestCase {

  function setUp(): void {
    parent::setUp();
    $this->createMembership();
  }


  function testAll() {
    $res = $this->client->memberships->all(['org_id'=>$this->org->id]);
    $this->assertNoError($res);
    $this->assertEquals(1, count($res->results));
    $this->assertEquals('membership', $res->results[0]['object']);
  }

  function testFind() {
    $res = $this->client->memberships->find($this->membership->id);
    $this->assertNoError($res);
    $this->assertEquals('membership', $res->object);
  }

  function testCreate() {
    $this->testDelete();
    $res = $this->client->memberships->create([
        'org_id' => $this->org->id,
        'user_id' => $this->user->id
      ]);
    $this->assertNoError($res);
    $this->assertEquals('membership', $res->object);
    $this->assertMatchesRegularExpression('/^mb_/', $res->id);
  }

  function testUpdate() {
    $this->assertEquals([], $this->membership->permissions);
    $res = $this->client->memberships->update($this->membership->id, ['permissions'=>['one','two']]);
    $this->assertNoError($res);
    $this->assertEquals(['one','two'], $res->permissions);
  }

  function testDelete() {
    $res = $this->client->memberships->delete($this->membership->id);
    $this->assertNoError($res);
  }

}

?>
