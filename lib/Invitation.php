<?php

namespace AuthRocket;

class Invitation extends Resource {

  function __construct($client) {
    parent::__construct($client);
    $this->path        = 'invitations';
    $this->rootElement = 'invitation';
  }


  public function invite($id, $params=null) {
    if (!is_string($id))
      throw new Error('$id must be a string');
    $path = $this->path . '/' . urlencode($id) . '/invite';
    $params = $this->buildRoot($this->rootElement, $params);
    return $this->client->post($path, $params);
  }

}

?>
