# BadBot for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/xqus/bad-bot.svg?style=flat)](https://packagist.org/packages/xqus/bad-bot)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/xqus/bad-bot/run-tests.yml?branch=main&label=tests&style=flat)](https://github.com/xqus/bad-bot/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/xqus/bad-bot/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat)](https://github.com/xqus/bad-bot/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/xqus/bad-bot.svg?style=flat)](https://packagist.org/packages/xqus/bad-bot)

A lightweight package that blocks unwanted crawlers, AI agents, scrapers, and high‑frequency visitors while allowing legitimate bots such as Googlebot and Bingbot to pass.


## Installation

You can install the package via composer:

```bash
composer require xqus/bad-bot
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="bad-bot-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="bad-bot-views"
```

## Usage

Add the middelware you want to run to your `bootstrap\app.php` file (see the [official documentation](https://laravel.com/docs/12.x/middleware#global-middleware))

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->append(xqus\BadBot\Middleware\BadBotMiddleware::class); // blocks marked ip addresses
    $middleware->append(xqus\BadBot\Middleware\ThrottleMiddleware::class); // blocks noisy bots, but allows whitelisted bots (Google, Bing, etc)
    $middleware->append(xqus\BadBot\Middleware\UserAgentMiddleWare::class); // blocks bots based on user-agent
})
```
To build a new robots.txt run

```bash
php artisan badbot:update-txt
```

Be aware that this command overwrites your current `public/robots.txt` file. 

### Custom error handling

By default an exception extending `Symfony\Component\HttpKernel\Exception\HttpException` will be called when an request is blocked. This will render the default HTTP error page for that error code. 

If you want to handle errors differently the following exceptions exists: 
```
xqus\BadBot\Exceptions\RequestRateLimitedException
xqus\BadBot\Exceptions\UserAgentBlockedException
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [xqus](https://github.com/xqus)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
