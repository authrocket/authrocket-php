<?php

namespace AuthRocket;

class Response {

  public $errors = [];
  public $fields = [];
  public $results = [];
  public $metadata = null;


  static function parseResponse($res) {
    $data     = [];
    $errors   = [];
    $metadata = [];
    $body     = self::decodeBody($res);

    if (isset($body['errors'])) {
      foreach ($body['errors'] as $attr => $msgs) {
        $errors += $msgs;
      }
      unset($body['errors']);
      $metadata = $body;
    } elseif (isset($body['collection'])) {
      $data = $body['collection'];
      unset($body['collection']);
      $metadata = $body;
    } elseif ($res->getStatusCode() == 215) {
      $metadata = $body;
    } else {
      $data = $body;
    }
    if (($res->getStatusCode() == 409 || $res->getStatusCode() == 422) && count($errors) == 0) {
      array_push($errors, 'Validation error');
    }
    return new Response($data, $metadata, $errors);
  }

  static function decodeBody($res) {
    $body = (string) $res->getBody();

    if ($res->hasHeader('Content-Type') && preg_match('/^application\/json/', $res->getHeader('Content-Type')[0])) {
      $body = json_decode($body, true);

      if (json_last_error() != JSON_ERROR_NONE) {
        throw new UnparsableResponse(json_last_error_msg());
      }
    }

    return $body;
  }


  function __construct($data, $metadata, $errors) {
    $this->errors   = $errors;
    $this->metadata = $metadata;
    $this->fields = $this->results = $data;
  }

  function hasErrors() {
    return count($this->errors) > 0;
  }

  function errorMessages() {
    return implode('; ', $this->errors);
  }

  function toArray() {
    return $this->fields;
  }

  function __get($name) {
    return isset($this->fields[$name]) ? $this->fields[$name] : null;
  }

  function hasMore() {
    if (isset($this->metadata['more_results'])) {
      return $this->metadata['more_results'];
    } else {
      return null;
    }
  }

}
?>
