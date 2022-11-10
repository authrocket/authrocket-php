<?php

namespace AuthRocket;

class Session extends Resource {

  function __construct($client) {
    parent::__construct($client);
    $this->path        = 'sessions';
    $this->rootElement = 'session';
  }

  function fromToken($token, $params=[]) {
    if (!$this->client->getDefaultJwtKey() && $this->client->getLoginrocketUrl()) {
      return $this->fromTokenWithDynamicKey($token, $params);
    } else {
      return $this->fromTokenWithStaticKey($token, $params);
    }
  }

  private function fromTokenWithStaticKey($token, $params=[]) {
    $jwtKey = $this->client->getDefaultJwtKey();
    if (!is_string($jwtKey)) {
      throw new Error("Missing jwtKey - set LOGINROCKET_URL, AUTHROCKET_JWT_KEY, or new AuthRocket(['loginrocketUrl'=>... or 'jwtKey'=>...])");
    }
    $jwtKey = trim($jwtKey);

    if (strlen($jwtKey) > 256) {
      $algo = 'RS256';
      if (!preg_match('/^-----BEGIN /', $jwtKey)) {
        $jwtKey = preg_replace('/[^\n]{64}/', "$0\n", $jwtKey);
        $jwtKey = "-----BEGIN PUBLIC KEY-----\n".$jwtKey."\n-----END PUBLIC KEY-----";
      }
    } else {
      $algo = 'HS256';
    }

    return $this->verifyAndParse($token, $jwtKey, $algo);
  }

  private function fromTokenWithDynamicKey($token, $params=[]) {
    if (!$token) return null;
    try {
      $hdr = explode('.', $token)[0];
      $jwtHeader = \Firebase\JWT\JWT::jsonDecode(\Firebase\JWT\JWT::urlsafeB64Decode($hdr));
    } catch (\Exception $e) {
      return null;
    }
    if (!$jwtHeader || !$jwtHeader->kid) return null;
    $kid = $jwtHeader->kid;
    if (!Loginrocket::getJwkSetKey($kid)) {
      $this->client->loginrocket->loadJwkSet();
    }
    if ($jwk = Loginrocket::getJwkSetKey($kid)) {
      return $this->verifyAndParse($token, $jwk['key'], $jwk['algo']);
    }
    return null;
  }

  private function verifyAndParse($token, $jwtKey, $algo) {
    \Firebase\JWT\JWT::$leeway = 5;
    try {
      $jwt = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($jwtKey, $algo), [$algo]);
    } catch (\UnexpectedValueException $e) {
      return null;
    }
    return $this->jwtToSession($jwt, $token);
  }

  private function jwtToSession($jwt, $token) {
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
      $user['memberships'] = [];
      foreach ($jwt['orgs'] as $m) {
        $m = (array)$m;
        foreach (['cs', 'name', 'perm', 'ref', 'selected'] as $attr) {
          if (!isset($m[$attr]))
            $m[$attr] = null;
        }
        $m2 = [
          'object'      => 'membership',
          'id'          => $m['mid'],
          'permissions' => $m['perm'],
          'selected'    => $m['selected'],
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
        array_push($user['memberships'], $m2);
      }
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
