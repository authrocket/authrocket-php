<?php

namespace AuthRocket;

abstract class Resource {

  protected $client;
  protected $path;
  protected $rootElement;

  function __construct($client) {
    $this->client = $client;
  }


  function all($params=null) {
    return $this->client->get($this->path, $params);
  }

  function first($params=[]) {
    $params = array_merge($params, ["max_results"=>1]);
    $res = $this->all($params);
    if (count($res->results) == 0) {
      return null;
    } else {
      return new Response($res->results[0], $res->metadata, $res->errors);
    }
  }

  function find($id, $params=null) {
    if (!is_string($id))
      throw new Error('$id must be a string (is a '.gettype($id).')');
    $path = $this->path . '/' . urlencode($id);
    return $this->client->get($path, $params);
  }

  function create(array $params) {
    $params = $this->buildRoot($this->rootElement, $params);
    return $this->client->post($this->path, $params);
  }

  function update($id, array $params) {
    if (!is_string($id))
      throw new Error('$id must be a string (is a '.gettype($id).')');
    $path = $this->path . '/' . urlencode($id);
    $params = $this->buildRoot($this->rootElement, $params);
    return $this->client->put($path, $params);
  }

  function delete($id, $params=null) {
    if (!is_string($id))
      throw new Error('$id must be a string (is a '.gettype($id).')');
    $path = $this->path . '/' . urlencode($id);
    return $this->client->delete($path, $params);
  }



  protected function buildRoot($root, $params) {
    if (!isset($params[$root])) {
      if (isset($params['request'])) {
        $req = $params['request'];
        unset($params['request']);
        $params = [
          $root     => $params,
          'request' => $req
        ];
      } else {
        $params = [$root => $params];
      }
    }
    return $params;
  }

}

?>
