<?php

namespace AuthRocket;

class Loginrocket extends Resource {

  // @private
  public static $jwkSet = [];

  // @private
  function loadJwkSet() {
    $url = 'connect/jwks';
    $res = $this->client->getLrApi()->get($url);
    $this->client->checkError($res, $url);
    $res = Response::parseResponse($res);

    foreach($res->keys as $h) {
      $jwtKey = preg_replace('/[^\n]{64}/', "$0\n", $h['x5c'][0]);
      $jwtKey = "-----BEGIN PUBLIC KEY-----\n".$jwtKey."\n-----END PUBLIC KEY-----";
      self::$jwkSet[$h['kid']] = [
        'key'  => $jwtKey,
        'algo' => $h['alg']
      ];
    }
  }

  static function getJwkSetKey($idx) {
    if (isset(self::$jwkSet[$idx]))
      return self::$jwkSet[$idx];
    else
      return null;
  }

}

?>
