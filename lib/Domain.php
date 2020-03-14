<?php

namespace AuthRocket;

class Domain extends Resource {

  function __construct($client) {
    parent::__construct($client);
    $this->path        = 'domains';
    $this->rootElement = 'domain';
  }

}

?>
