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
    $res = $this->client->users->find($this->user->username);
    $this->assertNoError($res);
    $this->assertEquals('user', $res->object);
  }

  function testCreate() {
    $res = $this->client->users->create(['user_type'=>'api']);
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

  function testDelete() {
    $res = $this->client->users->delete($this->user->id);
    $this->assertNoError($res);
    $res = $this->client->users->all();
    $this->assertNoError($res);
    $this->assertEquals(0, count($res->results));
  }

  function testAuthenticate() {
    try {
      $res = $this->client->users->authenticate($this->user->id, 'wrong');
      $this->assertFalse(true, "authenticate() should have thrown an exception");
    } catch (AuthenticationFailed $e) {
      // handled properly
    }

    $res = $this->client->users->authenticate($this->user->id, 'quick-fox-jumped-over-the-moon');
    $this->assertNoError($res);
    $this->assertEquals('user', $res->object);
    $this->assertTrue(is_string($res->token));
  }

  function testAuthenticateKey() {
    $res = $this->client->users->create(['user_type'=>'api']);
    $this->assertNoError($res);

    $res = $this->client->users->authenticateKey($res->api_key);
    $this->assertNoError($res);
    $this->assertEquals('user', $res->object);
  }

  function testRequestEmailVerification() {
    $res = $this->client->users->requestEmailVerification($this->user->id);
    $this->assertNoError($res);
    $this->assertEquals('user', $res->object);
    $this->assertTrue(is_string($res->token));
  }

  function testVerifyEmail() {
    $res = $this->client->users->requestEmailVerification($this->user->id);
    $this->assertNoError($res);

    $res = $this->client->users->verifyEmail($this->user->id, $res->token);
    $this->assertNoError($res);
    $this->assertEquals('user', $res->object);
  }

  function testGeneratePasswordToken() {
    $res = $this->client->users->generatePasswordToken($this->user->id);
    $this->assertNoError($res);
    $this->assertEquals('user', $res->object);
    $this->assertTrue(is_string($res->password_reset_token));
  }

  function testResetPasswordWithToken() {
    $res = $this->client->users->generatePasswordToken($this->user->id);
    $this->assertNoError($res);
    $this->assertRegExp('/^kpw_/', $res->password_reset_token);

    $res = $this->client->users->resetPasswordWithToken($this->user->id, [
      'token'                 => $res->password_reset_token,
      'password'              => 'how-now-brown-cow!',
      'password_confirmation' => 'how-now-brown-cow!'
    ]);
    $this->assertNoError($res);
    $this->assertEquals('user', $res->object);
  }

}

?>
