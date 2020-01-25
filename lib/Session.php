<?php

namespace AuthRocket;

class Session extends Resource {

  function __construct($client) {
    parent::__construct($client);
    $this->path        = 'sessions';
    $this->rootElement = 'session';
  }

  function fromToken($token, $params=[]) {
    $jwtKey = isset($params['jwtKey']) ? $params['jwtKey'] : $this->client->getDefaultJwtKey();
    if (!is_string($jwtKey)) {
      throw new Error('Missing jwtKey - must be provided via $params or new AuthRocket(...)');
    }

    if (strlen($jwtKey) > 256)
      $algo = ['RS256'];
    else
      $algo = ['HS256'];

    \Firebase\JWT\JWT::$leeway = 10;
    try {
      $jwt = \Firebase\JWT\JWT::decode($token, $jwtKey, $algo);
    } catch (\UnexpectedValueException $e) {
      return null;
    }
    $jwt = (array) $jwt;

    foreach (['aud', 'cs', 'fn', 'ln', 'm'] as $attr) {
      if (!isset($jwt[$attr]))
        $jwt[$attr] = null;
    }

    $user = [
      'object'     => 'user',
      'id'         => $jwt['uid'],
      'realm_id'   => $jwt['aud'],
      'username'   => $jwt['un'],
      'first_name' => $jwt['fn'],
      'last_name'  => $jwt['ln'],
      'name'       => $jwt['n'],
      'custom'     => $jwt['cs']
    ];
    if ($jwt['m']) {
      $mbs = $user['memberships'] = [];
      foreach ($jwt['m'] as $m) {
        $m = (array)$m;
        foreach (['cs', 'o', 'oid', 'ocs', 'p'] as $attr) {
          if (!isset($m[$attr]))
            $m[$attr] = null;
        }
        $m2 = [
          'object'      => 'membership',
          'permissions' => $m['p'],
          'user_id'     => $jwt['uid'],
          'org_id'      => $m['oid'],
          'custom'      => $m['cs']
        ];
        if ($m['o']) {
          $m2['org'] = [
            'object'   => 'org',
            'id'       => $m['oid'],
            'realm_id' => $jwt['aud'],
            'name'     => $m['o'],
            'custom'   => $m['ocs']
          ];
        }
        array_push($mbs, $m2);
      }
      $user['memberships'] = $mbs;
    }
    $session = [
      'object'     => 'session',
      'id'         => $jwt['tk'],
      'created_at' => $jwt['iat'],
      'expires_at' => $jwt['exp'],
      'token'      => $token,
      'user_id'    => $jwt['uid'],
      'user'       => $user
    ];
    
    return new Response($session, [], []);
  }

}

?>
