# Deduplicate Queues for Laravel

[![Packagist](https://badgen.net/packagist/v/ankurk91/laravel-dedupe-queue)](https://packagist.org/packages/ankurk91/laravel-dedupe-queue)
[![GitHub-tag](https://badgen.net/github/tag/ankurk91/laravel-dedupe-queue)](https://github.com/ankurk91/laravel-dedupe-queue/tags)
[![License](https://badgen.net/packagist/license/ankurk91/laravel-dedupe-queue)](LICENSE.txt)
[![Downloads](https://badgen.net/packagist/dt/ankurk91/laravel-dedupe-queue)](https://packagist.org/packages/ankurk91/laravel-dedupe-queue/stats)
[![GH-Actions](https://github.com/ankurk91/laravel-dedupe-queue/workflows/tests/badge.svg)](https://github.com/ankurk91/laravel-dedupe-queue/actions)
[![codecov](https://codecov.io/gh/ankurk91/laravel-dedupe-queue/branch/main/graph/badge.svg)](https://codecov.io/gh/ankurk91/laravel-dedupe-queue)

Prevent duplicate jobs in Laravel php framework.

> **Warning**
> This package is in its early stage and may have some edge case left.

## Why and How?

Laravel does not prevent duplicate jobs in
AWS [SQS Standard](https://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/standard-queues.html)
Queue.

This package takes advantage of the `UUID` assigned to each of the job pushed to the queue.
The UUID of the job does not get changed if the same message appears again in SQS queue.

This package injects a global middleware to all Jobs, Mailables, Notifications and Listeners;
which keep tracks of incoming jobs and discards any duplicates by checking the UUID.

## Installation

You can install the package via composer:

```bash
composer require "ankurk91/laravel-dedupe-queue"
```

The service provider will automatically register itself.

Optionally, You can publish the config file by:

```bash
php artisan vendor:publish --provider="Ankurk91\DedupeQueue\DedupeQueueServiceProvider"
```

> **Note**
> It is suggested to use Redis, Memcached or DynamoDB for faster Atomic Locks.

## Usage

Install and forget it.

You can keep this package enabled even for `sync` queue connection.

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

### Testing

```bash
composer test
```

### Security

If you discover any security issue, please email `pro.ankurk1[at]gmail[dot]com` instead of using the issue tracker.

### License

This package is licensed under [MIT License](https://opensource.org/licenses/MIT).
