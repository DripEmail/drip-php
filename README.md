# Drip API Wrapper - PHP

An object-oriented PHP wrapper for Drip's REST API v2.0

[![Drip PHP API Client Tests](https://github.com/DripEmail/drip-php/actions/workflows/build.yml/badge.svg)](https://github.com/DripEmail/drip-php/actions/workflows/build.yml)

## Installation

Run `composer require dripemail/drip-php`

## Authentication

For private integrations, you may use your personal API Token (found
[here](https://www.getdrip.com/user/edit)) via the `api_key` setting:

```php
$client = new \Drip\Client(["account_id" => "YOUR_ACCOUNT_ID", "api_key" => "YOUR_API_KEY"]);
```

For public integrations, pass in the user's OAuth token via the `access_token`
setting:

```php
$client = new \Drip\Client(["account_id" => "YOUR_ACCOUNT_ID", "access_token" => "YOUR_ACCESS_TOKEN"]);
```

Your account ID can be found [here](https://www.getdrip.com/settings).
Most API actions require an account ID, with the exception of methods like
the "list accounts" endpoint.


## PHP version support

We attempt to support versions of PHP that are supported upstream: http://php.net/supported-versions.php

For the actual supported list, see `.github/workflows/build.yml`.
