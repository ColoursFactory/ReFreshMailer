# ReFreshMailer - ReFreshed REST API client for freshmail.pl

With this package usage of freshmailer.pl REST API will be more comfortable.

It uses adapters to provide right PSR-7 compatible HTTP service.

Actully implemented ones are:

- cURL
- Guzzle6

Feel free to commit new adapters :)

See https://freshmail.pl/api_section/jak-zaczac/ for massive documentation.

http://freshmail.pl

## Installation

Simply add to your `composer.json` file

```yaml
{
    "require": {
        "preclowski/refreshmailer": "dev-master"
    }
}
```

or, require directly using

```
composer require preclowski/refreshmailer
```

## Usage

```php
use Preclowski\ReFreshMailer\FreshMailClient;
use Preclowski\ReFreshMailer\Http\GuzzleHttpAdapter;
use GuzzleHttp\Client;

$options = [
    'apiKey' => 'abcdef1234567890',
    'apiSecret' => 'abcdef1234567890',
];

$httpAdapter = new GuzzleHttpAdapter(new Client());
// or
$httpAdapter = new CurlHttpAdapter();

/** @var HttpAdapterInterface $httpAdapter */
$client = new FreshMailClient($httpAdapter, 'https://api.freshmail.com/rest/', $options);

$campaigns = $client->doRequest('/campaigns');
```

## Reporting an issue or a feature request

Issues and feature requests are tracked in the Github issue tracker.