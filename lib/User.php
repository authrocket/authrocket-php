<?php

namespace AuthRocket;

// in nearly all cases, a Username can be used in place of $id

class User extends Resource {

  function __construct($client) {
    parent::__construct($client);
    $this->path        = 'users';
    $this->rootElement = 'user';
  }


  // $params = [
  //   'current_password' => 'old',
  //   'password' => 'new',
  //   'password_confirmation' => 'new'
  // ]
  public function updatePassword($id, $params) {
    if (!is_string($id))
      throw new Error('$id must be a string');
    $path = $this->path . '/' . urlencode($id) . '/update_password';
    $params = $this->buildRoot($this->rootElement, $params);
    return $this->client->put($path, $params);
  }

  public function authenticate($id, $password, $params=null) {
    if (!is_string($id))
      throw new Error('$id must be a string');
    if (!is_string($password))
      throw new Error('$password must be a string');
    $path = $this->path . '/' . urlencode($id) . '/authenticate';
    if (!is_array($params))
      $params = [];
    $params['password'] = $password;
    return $this->client->post($path, $params);
  }

  // $params = [
  //   'token' => 'kli_abcdefg',
  //   'code' => '123456'
  // ]
  public function authenticateCode($id, $params) {
    if (!is_string($id))
      throw new Error('$id must be a string');
    $path = $this->path . '/' . urlencode($id) . '/authenticate_code';
    $params = $this->buildRoot($this->rootElement, $params);
    return $this->client->post($path, $params);
  }

  public function authenticateKey($apiKey, $params=null) {
    if (!is_string($apiKey))
      throw new Error('$apiKey must be a string');
    $path = $this->path . '/authenticate_key';
    if (!is_array($params))
      $params = [];
    $params['api_key'] = $apiKey;
    return $this->client->post($path, $params);
  }

  public function requestEmailVerification($id, $params=null) {
    if (!is_string($id))
      throw new Error('$id must be a string');
    $path = $this->path . '/' . urlencode($id) . '/request_email_verification';
    return $this->client->post($path, $params);
  }

  public function verifyEmail($id, $token, $params=null) {
    if (!is_string($id))
      throw new Error('$id must be a string');
    if (!is_string($token))
      throw new Error('$token must be a string');
    $path = $this->path . '/' . urlencode($id) . '/verify_email';
    $params = $this->buildRoot($this->rootElement, $params);
    $params['user']['token'] = $token;
    return $this->client->post($path, $params);
  }

  public function generatePasswordToken($id, $params=null) {
    if (!is_string($id))
      throw new Error('$id must be a string');
    $path = $this->path . '/' . urlencode($id) . '/generate_password_token';
    return $this->client->post($path, $params);
  }

  // $params = [
  //   'token' => 'abcdefghijkl',
  //   'password' => 'new',
  //   'password_confirmation' => 'new'
  // ]
  public function resetPasswordWithToken($id, $params) {
    if (!is_string($id))
      throw new Error('$id must be a string');
    $path = $this->path . '/' . urlencode($id) . '/reset_password_with_token';
    $params = $this->buildRoot($this->rootElement, $params);
    return $this->client->post($path, $params);
  }

}

?>
