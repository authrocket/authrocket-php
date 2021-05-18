<?php

namespace AuthRocket;

class UserTest extends TestCase {

  function setUp() {
    parent::setUp();
    $this->createUser();
  }


  function testAll() {
    $this->createUser();
    $res = $this->client->users->all();
    $this->assertNoError($res);
    $this->assertEquals(2, count($res->results));
    $this->assertEquals('user', $res->results[0]['object']);
  }

  function testFind() {
    $res = $this->client->users->find($this->user->email);
    $this->assertNoError($res);
    $this->assertEquals('user', $res->object);
  }

  function testCreate() {
    $res = $this->client->users->create([
      'email' => 'alexander@example.com',
      'password' => 'how-now-brown-cow!'
    ]);
    $this->assertNoError($res);
    $this->assertEquals('user', $res->object);
    $this->assertRegExp('/^usr_/', $res->id);
  }

  function testUpdate() {
    $this->assertEquals('george', $this->user->first_name);
    $res = $this->client->users->update($this->user->id, ['first_name'=>'freddy']);
    $this->assertNoError($res);
    $this->assertEquals('freddy', $res->first_name);
  }

  function testUpdatePassword() {
    $res = $this->client->users->updatePassword($this->user->id, [
      'current_password'      => 'wrong',
      'password'              => 'how-now-brown-cow!',
      'password_confirmation' => 'how-now-brown-cow!',
    ]);
    $this->assertMatchesError('/Current password does not match/', $res);

    $res = $this->client->users->updatePassword($this->user->id, [
      'current_password'      => 'quick-fox-jumped-over-the-moon',
      'password'              => 'how-now-brown-cow!',
      'password_confirmation' => 'how-now-brown-cow!',
    ]);
    $this->assertNoError($res);
  }

  function testUpdateProfile() {
    $res = $this->client->users->updateProfile($this->user->id, [
      'email' => 'george2@example.com'
    ]);
    $this->assertNoError($res);
  }

  function testDelete() {
    $res = $this->client->users->delete($this->user->id);
    $this->assertNoError($res);
    $res = $this->client->users->all();
    $this->assertNoError($res);
    $this->assertEquals(0, count($res->results));
  }

  function testAuthenticate() {
    $res = $this->client->users->authenticate($this->user->id, [
      'password' => 'wrong'
    ]);
    $this->assertMatchesError('/Login failed/', $res);

    $res = $this->client->users->authenticate($this->user->email, [
      'password' => 'quick-fox-jumped-over-the-moon'
    ]);
    $this->assertNoError($res);
    $this->assertEquals('session', $res->object);
    $this->assertTrue(is_string($res->token));
  }

  function testRequestEmailVerification() {
    $res = $this->client->users->requestEmailVerification($this->user->id);
    $this->assertNoError($res);
    $this->assertEquals('token', $res->object);
    $this->assertRegExp('/^tve:/', $res->token);
  }

  function testVerifyEmail() {
    $res = $this->client->users->requestEmailVerification($this->user->id);
    $this->assertNoError($res);

    $res = $this->client->users->verifyEmail(['token'=>$res->token]);
    $this->assertNoError($res);
    $this->assertEquals('user', $res->object);
  }

  function testGeneratePasswordToken() {
    $res = $this->client->users->generatePasswordToken($this->user->id);
    $this->assertNoError($res);
    $this->assertEquals('token', $res->object);
    $this->assertRegExp('/^tpw:/', $res->token);
  }

  function testResetPasswordWithToken() {
    $res = $this->client->users->generatePasswordToken($this->user->id);
    $this->assertNoError($res);

    $res = $this->client->users->resetPasswordWithToken([
      'token'                 => $res->token,
      'password'              => 'how-now-brown-cow!',
      'password_confirmation' => 'how-now-brown-cow!'
    ]);
    $this->assertNoError($res);
    $this->assertEquals('session', $res->object);
  }

  function testAcceptInvitation() {
    $this->createOrg();
    $invite = $this->client->invitations->create([
      'email'           => 'freddy@example.com',
      'invitation_type' => 'org',
      'org_id'          => $this->org->id
    ]);
    $this->assertNoError($invite);

    $res = $this->client->users->acceptInvitation($this->user->id, [
      'token' => $invite->token
    ]);
    $this->assertNoError($res);
  }

}

?>
