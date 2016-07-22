<?php

namespace AuthRocket;

class Realm extends Resource {

  function __construct($client) {
    parent::__construct($client);
    $this->path        = 'realms';
    $this->rootElement = 'realm';
  }

  function reset($id, $params=null) {
    if (!is_string($id))
      throw new Error('$id must be a string');
    $path = $this->path . '/' . urlencode($id) . '/reset';
    return $this->client->post($path, $params);
  }

}

?>
