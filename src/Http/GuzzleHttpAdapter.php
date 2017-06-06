<?php

namespace Preclowski\ReFreshMailer\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;

/**
 * @author Konrad Pawlikowski <preclowski@gmail.com>
 * @link https://github.com/Preclowski/ReFreshMailer
 * @license MIT
 */
class GuzzleHttpAdapter implements HttpAdapterInterface
{
    /** @var ClientInterface */
    private $guzzle;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->guzzle = $client;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function doRequest($method, $url, array $params = [], array $headers = [])
    {
        $request = new Request($method, $url, $headers);

        return $this->guzzle->send($request);
    }
}