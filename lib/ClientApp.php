<?php

namespace AuthRocket;

class ClientApp extends Resource {

  function __construct($client) {
    parent::__construct($client);
    $this->path        = 'client_apps';
    $this->rootElement = 'client_app';
  }

}

?>
