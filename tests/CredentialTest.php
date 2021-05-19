<?php

namespace AuthRocket;

class CredentialTest extends TestCase {

  function setUp(): void {
    parent::setUp();
    $this->createUser();
    $this->credential = $this->user->credentials[0];
    $this->activateTotpAuthProvider();
  }


  function testFind() {
    $res = $this->client->credentials->find($this->credential['id']);
    $this->assertNoError($res);
    $this->assertEquals('credential', $res->object);
  }

  function testCreate() {
    $res = $this->client->credentials->create(['user_id'=>$this->user->id, 'auth_provider_id'=>$this->authProvider->id, 'credential_type'=>'totp', 'name'=>'Test']);
    $this->assertNoError($res);
    $this->assertEquals('credential', $res->object);
    $this->assertMatchesRegularExpression('/^crd_/', $res->id);
  }

  function testUpdate() {
    $res = $this->client->credentials->update($this->credential['id'], ['password'=>'93uairgejkfdlgd']);
    $this->assertNoError($res);
  }

  function testDelete() {
    $res = $this->client->credentials->delete($this->credential['id']);
    $this->assertNoError($res);
  }

  function testVerify() {
    $res = $this->client->credentials->create(['user_id'=>$this->user->id, 'auth_provider_id'=>$this->authProvider->id, 'credential_type'=>'totp', 'name'=>'Test']);
    $this->assertNoError($res);
    $res2 = $this->client->credentials->verify($res->id, ['code'=>'123456']);
    $this->assertMatchesError('/Verification failed/', $res2);
  }

}

?>
