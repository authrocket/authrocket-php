<?php

namespace AuthRocket;

/*
  usage:

  $client = new \AuthRocket\AuthRocket([
    'url'    => 'https://api-e2.authrocket.com/v2',
    'apiKey' => 'ko_SAMPLE',
    'realm'  => 'rl_SAMPLE',
    'jwtKey' => 'jsk_SAMPLE'
  ]);

  $client->orgs()->all();
  $client->orgs()->find('org_SAMPLE');
  $client->orgs()->create([...]);
  $client->orgs()->update('org_SAMPLE', [...]);
  $client->orgs()->delete('org_SAMPLE');

*/



class Error extends \Exception {}
class AuthenticationFailed extends Error {}
class UnparsableResponse extends Error {}
class RecordNotFound extends Error {}

class AuthRocket {

  const VERSION = '2.0.0';


  private $api;
  private $config = [];
  public $debug   = false;

  static function autoConfigure($params=[]) {
    $config = [
      'url'     => getenv('AUTHROCKET_URL'),
      'apiKey'  => getenv('AUTHROCKET_API_KEY'),
      'realm'   => getenv('AUTHROCKET_REALM'),
      'service' => getenv('AUTHROCKET_SERVICE'),
      'jwtKey'  => getenv('AUTHROCKET_JWT_KEY')
    ];
    $config = array_merge($config, $params);
    foreach ($config as $key => $val) {
      if (!$val)
        unset($config[$key]);
    }
    return new AuthRocket($config);
  }

  function __construct(array $params) {
    $this->setDefaultJwtKey(isset($params['jwtKey']) ? $params['jwtKey'] : null);

    $this->config['headers'] = [
      'Accept-Encoding' => 'gzip',
      'Content-Type' => 'application/json',
      'User-Agent' => "AuthRocket/php v".AuthRocket::VERSION,
      'Authrocket-Api-Key' => $params['apiKey']
    ];
    if (isset($params['service']))
      $this->config['headers']['Authrocket-Service'] = $params['service'];
    if (isset($params['realm']))
      $this->config['headers']['Authrocket-Realm'] = $params['realm'];

    $this->config['url'] = $params['url'];
    if ($this->config['url'][strlen($this->config['url'])-1] != '/')
      $this->config['url'] .= '/';
    $this->config['verifySsl'] = !isset($params['verifySsl']) || $params['verifySsl'];

    $this->authProviders();
    $this->credentials();
    $this->invitations();
    $this->memberships();
    $this->orgs();
    $this->realms();
    $this->sessions();
    $this->users();
  }

  public function getDefaultJwtKey() {
    return isset($this->config['jwtKey']) ? $this->config['jwtKey'] : null;
  }

  public function setDefaultJwtKey($jwtKey) {
    $this->config['jwtKey'] = $jwtKey;
  }

  public function setDefaultRealm($realmId) {
    $this->api = null;
    $this->config['headers']['Authrocket-Realm'] = $realmId;
  }

  protected function getApi() {
    if ($this->api)
      return $this->api;

    $this->api = new \GuzzleHttp\Client([
      'base_uri'        => $this->config['url'],
      'http_errors'     => false,  // don't throw on 4xx or 5xx
      'connect_timeout' => 10,
      'timeout'         => 50,
      'debug'           => $this->debug,
      'headers'         => $this->config['headers'],
      'verify'          => $this->config['verifySsl'] ? dirname(dirname(__FILE__)).'/data/ca-certificates.crt' : false,
    ]);

    return $this->api;
  }



  public function get($url, $params=null) {
    $res = $this->getApi()->get($url, ['query'=>$params]);
    $this->checkError($res, $url);
    return Response::parseResponse($res);
  }

  public function post($url, $fields=null) {
    $res = $this->getApi()->post($url, ['json'=>$fields]);
    $this->checkError($res, $url);
    return Response::parseResponse($res);
  }

  public function put($url, $fields=null) {
    $res = $this->getApi()->put($url, ['json'=>$fields]);
    $this->checkError($res, $url);
    return Response::parseResponse($res);
  }

  public function delete($url, $params=null) {
    $res = $this->getApi()->delete($url, ['json'=>$params]);
    $this->checkError($res, $url);
    return Response::parseResponse($res);
  }



  private function checkError($res, $url='') {
    $code = $res->getStatusCode();
    switch ($code) {
      case 401:
        throw new AuthenticationFailed("Authentication failed (API key is valid; this auth-related API call failed)");
        break;
      case 402:
        throw new Error("Account inactive; login to portal to check service status");
        break;
      case 403:
        throw new Error("Access denied; check your API credentials and permissions");
        break;
      case 404:
        throw new RecordNotFound("Not found: ".$url);
        break;
      case 409:
      case 422:
        // passthrough
        break;
      case 429:
        throw new Error('Rate limited; wait before trying again');
        break;
      default:
        if ($code >= 400 && $code <= 499) {
          throw new Error("Client error: $code -- ".(string)$res->getBody());
        }
        if ($code >= 500 && $code <= 599) {
          throw new Error("Server error: $code -- ".(string)$res->getBody());
        }
    }
  }





  public function authProviders() {
    if (!isset($this->authProviders))
      $this->authProviders = new AuthProvider($this);
    return $this->authProviders;
  }

  public function credentials() {
    if (!isset($this->credentials))
      $this->credentials = new Credential($this);
    return $this->credentials;
  }

  public function invitations() {
    if (!isset($this->invitations))
      $this->invitations = new Invitation($this);
    return $this->invitations;
  }

  public function memberships() {
    if (!isset($this->memberships))
      $this->memberships = new Membership($this);
    return $this->memberships;
  }

  public function orgs() {
    if (!isset($this->orgs))
      $this->orgs = new Org($this);
    return $this->orgs;
  }

  public function realms() {
    if (!isset($this->realms))
      $this->realms = new Realm($this);
    return $this->realms;
  }

  public function sessions() {
    if (!isset($this->sessions))
      $this->sessions = new Session($this);
    return $this->sessions;
  }

  public function users() {
    if (!isset($this->users))
      $this->users = new User($this);
    return $this->users;
  }


}

?>
