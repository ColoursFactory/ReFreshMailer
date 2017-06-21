<?php

namespace ColoursFactory\ReFreshMailer\HttpClient;

use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;

/**
 * {@inheritdoc}
 */
class FreshMailAuthenticationPlugin implements Plugin
{
    /** @var string */
    private $apiKey;
    /** @var string */
    private $apiSecret;

    /**
     * @param string $apiKey
     * @param string $apiSecret
     */
    public function __construct($apiKey, $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    /**
     * Handle the request and return the response coming from the next callable.
     *
     * @see http://docs.php-http.org/en/latest/plugins/build-your-own.html
     *
     * @param RequestInterface $request
     * @param callable $next Next middleware in the chain, the request is passed as the first argument
     * @param callable $first First middleware in the chain, used to to restart a request
     *
     * @return Promise Resolves a PSR-7 Response or fails with an Http\Client\Exception (The same as HttpAsyncClient).
     *
     * @throws \InvalidArgumentException
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        $headers = [
            'X-Rest-ApiKey' => $this->apiKey,
            'X-Rest-ApiSign' => $this->calculateSignature($request, $this->apiKey, $this->apiSecret),
        ];

        foreach ($headers as $name => $value) {
            $request = $request->withAddedHeader($name, $value);
        }

        return $next($request);
    }

    /**
     * @param RequestInterface $request
     * @param string $apiKey
     * @param string $apiSecret
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    private function calculateSignature(RequestInterface $request, $apiKey, $apiSecret)
    {
        return sha1(
            $apiKey .
            $request->getUri()->getPath() .
            $request->getBody()->getContents() .
            $apiSecret
        );
    }
}