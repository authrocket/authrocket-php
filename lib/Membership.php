<?php

namespace AuthRocket;

class Membership extends Resource {

  function __construct($client) {
    parent::__construct($client);
    $this->path        = 'memberships';
    $this->rootElement = 'membership';
  }

}

?>
