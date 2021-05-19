<?php

namespace AuthRocket;

class InvitationTest extends TestCase {

  function setUp(): void {
    parent::setUp();
    $this->createInvitation();
  }


  function testAll() {
    $res = $this->client->invitations->all();
    $this->assertNoError($res);
    $this->assertEquals(1, count($res->results));
    $this->assertEquals('invitation', $res->results[0]['object']);
  }

  function testFirst() {
    $res = $this->client->invitations->first();
    $this->assertNoError($res);
    $this->assertEquals('invitation', $res->object);
  }

  function testFind() {
    $res = $this->client->invitations->find($this->invitation->id);
    $this->assertNoError($res);
    $this->assertEquals('invitation', $res->object);
  }

  function testCreate() {
    $res = $this->client->invitations->create([
      'email'           => 'freddy@example.com',
      'invitation_type' => 'request'
    ]);
    $this->assertNoError($res);
    $this->assertEquals('invitation', $res->object);
    $this->assertMatchesRegularExpression('/^nvt_/', $res->id);
    $this->assertTrue(is_string($res->token));
  }

  function testUpdate() {
    $res = $this->client->invitations->update($this->invitation->id, [
      'email' => 'freddy2@example.com'
    ]);
    $this->assertNoError($res);
  }

  function testDelete() {
    $res = $this->client->invitations->delete($this->invitation->id);
    $this->assertNoError($res);
  }

  function testInvite() {
    $res = $this->client->invitations->invite($this->invitation->id);
    $this->assertNoError($res);
  }

}

?>
