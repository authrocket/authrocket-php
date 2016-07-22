<?php

namespace AuthRocket;

// in many cases, a providerType can be used in place of $id

class AuthProvider extends Resource {

  function __construct($client) {
    parent::__construct($client);
    $this->path        = 'auth_providers';
    $this->rootElement = 'auth_provider';
  }

  // $params = [
  //   'redirect_uri' => 'https://...',
  //   'nonce'        => 'randomString'  // optional
  // ]
  public function authorizeUrls($params) {
    $path = $this->path . '/authorize';
    return $this->client->get($path, $params);
  }

  // $params = [
  //   'redirect_uri' => 'https://...',
  //   'nonce'        => 'randomString'  // optional
  // ]
  public function authorizeUrl($id, $params) {
    if (!is_string($id))
      throw new Error('$id must be a string');
    $path = $this->path . '/' . urlencode($id) . '/authorize';
    return $this->client->get($path, $params);
  }

  // $params = [
  //   'code'  => '...',
  //   'nonce' => 'randomString',  // optional
  //   'state' => '...'
  // ]
  public function authorize($params) {
    $path = $this->path . '/authorize';
    return $this->client->post($path, $params);
  }

  // $params = [
  //   'access_token' => '...'
  // ]
  public function authorizeToken($id, $params) {
    if (!is_string($id))
      throw new Error('$id must be a string');
    $path = $this->path . '/' . urlencode($id) . '/authorize';
    return $this->client->post($path, $params);
  }

}

?>
