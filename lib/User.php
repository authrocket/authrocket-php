<?php

namespace AuthRocket;

// in nearly all cases, an Email can be used in place of $id

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
    if (!is_array($params))
      throw new Error('$params must be an array');
    $path = $this->path . '/' . urlencode($id) . '/update_password';
    $params = $this->buildRoot($this->rootElement, $params);
    return $this->client->put($path, $params);
  }

  // $params = [
  //   'email' => '...',
  //   'first_name' => '...',
  //   'last_name' => '...',
  //   'password' => 'new',
  //   'password_confirmation' => 'new',
  //   'username' => '...'
  // ]
  public function updateProfile($id, $params) {
    if (!is_string($id))
      throw new Error('$id must be a string');
    if (!is_array($params))
      throw new Error('$params must be an array');
    $path = $this->path . '/' . urlencode($id) . '/profile';
    $params = $this->buildRoot($this->rootElement, $params);
    return $this->client->put($path, $params);
  }

  // $params = [
  //   'password' => 'secret'
  // ]
  public function authenticate($id, $params) {
    if (!is_string($id))
      throw new Error('$id must be a string');
    if (!is_array($params))
      throw new Error('$params must be an array');
    $path = $this->path . '/' . urlencode($id) . '/authenticate';
    $params = $this->buildRoot($this->rootElement, $params);
    return $this->client->post($path, $params);
  }

  // $params = [
  //   'token' => 'kli:abcdefg',
  //   'code' => '123456'
  // ]
  public function authenticateToken($id, $params) {
    if (!is_string($id))
      throw new Error('$id must be a string');
    if (!is_array($params))
      throw new Error('$params must be an array');
    $path = $this->path . '/' . urlencode($id) . '/authenticate_token';
    $params = $this->buildRoot($this->rootElement, $params);
    return $this->client->post($path, $params);
  }

  public function requestEmailVerification($id, $params=null) {
    if (!is_string($id))
      throw new Error('$id must be a string');
    $path = $this->path . '/' . urlencode($id) . '/request_email_verification';
    return $this->client->post($path, $params);
  }

  // $params = [
  //   'token' => 'abcdefghijkl'
  // ]
  public function verifyEmail($params) {
    if (!is_array($params))
      throw new Error('$params must be an array');
    $path = $this->path . '/verify_email';
    $params = $this->buildRoot($this->rootElement, $params);
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
  public function resetPasswordWithToken($params) {
    if (!is_array($params))
      throw new Error('$params must be an array');
    $path = $this->path . '/reset_password_with_token';
    $params = $this->buildRoot($this->rootElement, $params);
    return $this->client->post($path, $params);
  }

  // $params = [
  //   'token' => 'abcdefghijkl',
  // ]
  public function acceptInvitation($id, $params) {
    if (!is_string($id))
      throw new Error('$id must be a string');
    if (!is_array($params))
      throw new Error('$params must be an array');
    $path = $this->path . '/' . urlencode($id) . '/accept_invitation';
    $params = $this->buildRoot($this->rootElement, $params);
    return $this->client->post($path, $params);
  }

}

?>
