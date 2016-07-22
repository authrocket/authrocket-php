# AuthRocket

[AuthRocket](https://authrocket.com/) provides Auth as a Service, making it quick and easy to add signups, logins, social auth, a full user management UI, and much more to your app.

The `authrocket` PHP library covers all of our Core API. It also covers select portions of the Extended API.


## Installation

The library is designed to be installed using `composer`. It should also be usable using any other method typically supported by composer-compatible packages.

For installation, run `php composer.phar require authrocket/authrocket`.

Alternatively, add `"authrocket/authrocket": "<2"` to the `require` section of your composer.json and run `php composer.phar install`.


## Client Basics

### Using environment variables

If you are using environment variables to manage external services like AuthRocket, then it's very easy to initialize the AuthRocket client:

```php
$client = \AuthRocket\AuthRocket::autoConfigure();
```

Ensure these environment variables are set:

```bash
AUTHROCKET_API_KEY    = ko_SAMPLE
AUTHROCKET_URL        = https://api-e1.authrocket.com/v1
AUTHROCKET_REALM      = rl_SAMPLE   # optional
AUTHROCKET_JWT_SECRET = jsk_SAMPLE  # optional
```

`AUTHROCKET_URL` must be updated based on what cluster your account is provisioned on.

`AUTHROCKET_REALM` and `AUTHROCKET_JWT_SECRET` are optional. If you are using multiple realms, we recommend building a new client for each realm, just setting `realm` and `jwtSecret`:

```php
$client = \AuthRocket\AuthRocket::autoConfigure([
  'realm'     => 'rl_SAMPLE',
  'jwtSecret' => 'jsk_SAMPLE'
]);
```


### Direct configuration

It's also possible to configure the AuthRocket client instance directly:

```php
$client = new \AuthRocket\AuthRocket([
  'apiKey'    => 'ko_SAMPLE',
  'url'       => 'https://api-e1.authrocket.com/v1',
  'realm'     => 'rl_SAMPLE',
  'jwtSecret' => 'jsk_SAMPLE'
]);
```


## Usage

Documentation is provided on our site:

* [AuthRocket Quickstart](https://authrocket.com/docs/quickstart)
* [PHP Integration Guide](https://authrocket.com/docs/php/integration)
* [PHP Library Usage](https://authrocket.com/docs/php/intro)
* [API Documentation with PHP examples](https://authrocket.com/docs/api/memberships)
