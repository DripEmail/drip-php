# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

* Your improvement here!

## 1.4.2

* Update composer.json to allow newer PHP.

## 1.4.1

* [#32](https://github.com/DripEmail/drip-php/pull/32) - Use Guzzle 7
* Related to previous, drop support for PHP older than 7.2.

## 1.4.0

* [#31](https://github.com/DripEmail/drip-php/pull/31) - Use Bearer authentication

## 1.3.0

* [#22](https://github.com/DripEmail/drip-php/pull/22) - Add `#fetch_subscribers` method. (@mticciati)
* [#30](https://github.com/DripEmail/drip-php/pull/30) - Add `#fetch_subscriber_campaigns` method. (@hannesvdvreken)

## 1.2.0

* [#20](https://github.com/DripEmail/drip-php/pull/20) - Return error response instead of throwing exception. (@joeldodge79)

## 1.1.0

* [#15](https://github.com/DripEmail/drip-php/pull/15) - Added `\Drip\Client#create_or_update_subscribers` method (@j831)
* [#17](https://github.com/DripEmail/drip-php/pull/17) - Make private methods protected so that people can subclass

## 1.0.0

*This version breaks backwards compatibility, as per semver.*

- Set up composer package
- Make PSR-4 compatible
- Move to namespace `\Drip\Client`
- Pass account_id into client constructor (matches semantics of Ruby API client better)
- Remove some client methods to reduce abstraction leakage:
  - `\Drip\Client#make_request`
  - `\Drip\Client#get_request_info`
  - `\Drip\Client#get_error_message`
  - `\Drip\Client#get_error_code`
  - `\Drip\Client#_parse_error`
- Switch to Guzzle HTTP Client
- Allow setting of API endpoint
- Return response object instead of array.
- Fairly complete test suite
- Code coverage metrics
- Linter

## 0.0.2

* Introduces Composer

## 0.0.1

* Initial release
