<?php

namespace Preclowski\ReFreshMailer\HttpAdapter;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use Preclowski\ReFreshMailer\Exception\HttpAdapterException;

/**
 * @author Konrad Pawlikowski <preclowski@gmail.com>
 * @link https://github.com/Preclowski/ReFreshMailer
 * @license MIT
 */
class GuzzleAdapter implements HttpAdapterInterface
{
    /** @var ClientInterface */
    private $client;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->client = new Client([
            'base_url' => $options['host'],
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Preclowski\ReFreshMailer\Exception\HttpAdapterException
     */
    public function sendRequest($method, $url, array $params = [], array $headers = [])
    {
        $request = new Request($method, $url, $headers);

        try {
            return $this->client->send($request);
        } catch (BadResponseException $e) {
            throw new HttpAdapterException($e->getMessage(), $e->getCode(). $e);
        }
    }
}