<?php

namespace AuthRocket;

class InvitationTest extends TestCase {

  function setUp() {
    parent::setUp();
    $this->createInvitation();
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
    $this->assertRegExp('/^nvt_/', $res->id);
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
