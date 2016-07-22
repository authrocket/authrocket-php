<?php

namespace AuthRocket;

class Org extends Resource {

  function __construct($client) {
    parent::__construct($client);
    $this->path        = 'orgs';
    $this->rootElement = 'org';
  }

}

?>
