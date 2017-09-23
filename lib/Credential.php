<?php

namespace AuthRocket;

class Credential extends Resource {

  function __construct($client) {
    parent::__construct($client);
    $this->path        = 'credentials';
    $this->rootElement = 'credential';
  }


  // $params = [
  //   'code' => '123456'
  // ]
  public function verify($id, $params) {
    if (!is_string($id))
      throw new Error('$id must be a string');
    $path = $this->path . '/' . urlencode($id) . '/verify';
    $params = $this->buildRoot($this->rootElement, $params);
    return $this->client->post($path, $params);
  }

}

?>
