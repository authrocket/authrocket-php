<?php

namespace AuthRocket;

class Session extends Resource {

  function __construct($client) {
    parent::__construct($client);
    $this->path        = 'sessions';
    $this->rootElement = 'session';
  }

  function fromToken($token, $params=[]) {
    $jwtSecret = isset($params['jwtSecret']) ? $params['jwtSecret'] : $this->client->config['jwtSecret'];
    if (!is_string($jwtSecret)) {
      throw new Error('Missing jwtSecret - must be provided via $params or new AuthRocket(...)');
    }
    
    \Firebase\JWT\JWT::$leeway = 10;
    try {
      $jwt = \Firebase\JWT\JWT::decode($token, $jwtSecret, ['HS256']);
    } catch (\UnexpectedValueException $e) {
      return null;
    }
    $jwt = (array) $jwt;

    foreach (['aud', 'fn', 'ln', 'm'] as $attr) {
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
      'name'       => $jwt['n']
    ];
    if ($jwt['m']) {
      $mbs = $user['memberships'] = [];
      foreach ($jwt['m'] as $m) {
        $m2 = [
          'object'      => 'membership',
          'permissions' => $m['p'],
          'user_id'     => $jwt['uid'],
          'org_id'      => $m['oid']
        ];
        if ($m['oid']) {
          $m2['org'] = [
            'object'   => 'org',
            'id'       => $m['oid'],
            'realm_id' => $jwt['aud'],
            'name'     => $m['o']
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
