# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

- Set up composer package
- Make PSR-4 compatible
- Move to namespace `\Drip\Client`
- Add initial tests using PHPUnit
- Pass account_id into client constructor
- Make some client methods private:
  - `\Drip\Client#make_request`
  - `\Drip\Client#get_request_info`
  - `\Drip\Client#get_error_message`
  - `\Drip\Client#get_error_code`
  - `\Drip\Client#_parse_error`
