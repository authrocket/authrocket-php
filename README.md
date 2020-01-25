# AuthRocket

[AuthRocket](https://authrocket.com/) provides Auth as a Service, making it quick and easy to add signups, logins, social auth, a full user management UI, and much more to your app.

The `authrocket` PHP library covers all of our Core API. It also covers select portions of the Configuration API.


## Installation

The library is designed to be installed using `composer`. It should also be usable using any other method typically supported by composer-compatible packages.

For installation, run:
```bash
composer require authrocket/authrocket

# alternate:
php composer.phar require authrocket/authrocket
```

Or, add `"authrocket/authrocket": "^2"` to the `require` section of your composer.json and run `composer install`.

You can also download `authrocket.phar`, a .zip, or a .tar.gz of the latest release directly from GitHub: https://github.com/authrocket/authrocket-php/releases/latest


## Client Basics

### Using environment variables

If you are using environment variables to manage external services like AuthRocket, then it's very easy to initialize the AuthRocket client:

```php
$client = \AuthRocket\AuthRocket::autoConfigure();
```

Ensure these environment variables are set:

```bash
AUTHROCKET_API_KEY = ko_SAMPLE
AUTHROCKET_URL     = https://api-e2.authrocket.com/v2
AUTHROCKET_REALM   = rl_SAMPLE   # optional
AUTHROCKET_JWT_KEY = jsk_SAMPLE  # optional
```

`AUTHROCKET_URL` may vary based on what cluster your account is provisioned on.

`AUTHROCKET_REALM` and `AUTHROCKET_JWT_KEY` are optional. If you are using multiple realms, we recommend building a new client for each realm, directly setting `realm` and `jwtKey`:

```php
$client = \AuthRocket\AuthRocket::autoConfigure([
  'realm'  => 'rl_SAMPLE',
  'jwtKey' => 'jsk_SAMPLE'
]);
```


### Direct configuration

It's also possible to configure the AuthRocket client instance directly:

```php
$client = new \AuthRocket\AuthRocket([
  'apiKey' => 'ko_SAMPLE',
  'url'    => 'https://api-e2.authrocket.com/v2',
  'realm'  => 'rl_SAMPLE',
  'jwtKey' => 'jsk_SAMPLE'
]);
```


## Usage

Documentation is provided on our site:

* [AuthRocket Quickstart](https://authrocket.com/docs/quickstart)
* [PHP Integration Guide](https://authrocket.com/docs/php/integration)
* [PHP Library Usage](https://authrocket.com/docs/php/intro)
* [API Documentation with PHP examples](https://authrocket.com/docs/api/memberships)


## Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create new Pull Request


## License

MIT
