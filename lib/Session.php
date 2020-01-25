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

    foreach (['cs', 'email_verified', 'given_name', 'family_name', 'orgs', 'preferred_username', 'ref'] as $attr) {
      if (!isset($jwt[$attr]))
        $jwt[$attr] = null;
    }

    $user = [
      'object'             => 'user',
      'id'                 => $jwt['sub'],
      'realm_id'           => $jwt['rid'],
      'username'           => $jwt['preferred_username'],
      'first_name'         => $jwt['given_name'],
      'last_name'          => $jwt['family_name'],
      'name'               => $jwt['name'],
      'email'              => $jwt['email'],
      'email_verification' => $jwt['email_verified'] ? 'verified' : 'none',
      'reference'          => $jwt['ref'],
      'custom'             => $jwt['cs']
    ];
    if ($jwt['orgs']) {
      $mbs = $user['memberships'] = [];
      foreach ($jwt['orgs'] as $m) {
        $m = (array)$m;
        foreach (['cs', 'name', 'perm', 'ref'] as $attr) {
          if (!isset($m[$attr]))
            $m[$attr] = null;
        }
        $m2 = [
          'object'      => 'membership',
          'id'          => $m['mid'],
          'permissions' => $m['perm'],
          'user_id'     => $jwt['sub'],
          'org_id'      => $m['oid'],
          'org' => [
            'object'    => 'org',
            'id'        => $m['oid'],
            'realm_id'  => $jwt['rid'],
            'name'      => $m['name'],
            'reference' => $m['ref'],
            'custom'    => $m['cs']
          ]
        ];
        array_push($mbs, $m2);
      }
      $user['memberships'] = $mbs;
    }
    $session = [
      'object'     => 'session',
      'id'         => $jwt['sid'],
      'created_at' => $jwt['iat'],
      'expires_at' => $jwt['exp'],
      'token'      => $token,
      'user_id'    => $jwt['sub'],
      'user'       => $user
    ];
    
    return new Response($session, [], []);
  }

}

?>
