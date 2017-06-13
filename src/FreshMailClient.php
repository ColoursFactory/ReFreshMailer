<?php

namespace ColoursFactory\ReFreshMailer;

use ColoursFactory\ReFreshMailer\Exception\FreshMailApiException;
use ColoursFactory\ReFreshMailer\HttpClient\FreshMailAuthenticationPlugin;
use ColoursFactory\ReFreshMailer\HttpClient\HttpClientFactory;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Exception\HttpException;
use Http\Client\HttpClient;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\UriFactoryDiscovery;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Konrad Pawlikowski <preclowski@gmail.com>
 * @link https://github.com/Preclowski/ReFreshMailer
 * @license MIT
 */
class FreshMailClient
{
    /** @var HttpClient */
    private $httpClient;

    /**
     * @param HttpClient|null $httpClient
     * @param string $host
     * @param string $apiKey
     * @param string $apiSecret
     */
    public function __construct(HttpClient $httpClient = null, $host, $apiKey, $apiSecret)
    {
        $plugins = [
            new AddHostPlugin(UriFactoryDiscovery::find()->createUri($host)),
            new FreshMailAuthenticationPlugin($apiKey, $apiSecret),
            new HeaderDefaultsPlugin([
                'Content-Type' => 'application/json',
            ]),
        ];

        $this->httpClient = HttpClientFactory::create($plugins, $httpClient);
        $this->messageFactory = MessageFactoryDiscovery::find();
    }

    /**
     * @param string $url
     * @param array $params
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     * @throws \ColoursFactory\ReFreshMailer\Exception\FreshMailApiException
     */
    public function doRequest($url, array $params = [])
    {
        $method = empty($params) ? 'GET' : 'POST';

        $request = $this->messageFactory->createRequest($method, $url, [], json_encode($params));

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (HttpException $e) {
            throw new FreshMailApiException($e->getMessage(), $e->getRequest(), $e->getResponse(), $e);
        }

        return json_decode($this->handleResponse($response)->getBody()->getContents(), true);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     *
     * @throws \RuntimeException
     * @throws \ColoursFactory\ReFreshMailer\Exception\FreshMailApiException
     */
    private function handleResponse(ResponseInterface $response)
    {
        $contentType = $response->getHeader('Content-Type');

        if ('application/zip' === $contentType) {
            $filename = $response->getHeader('Filename');
            $filePath = '/tmp/'. $filename;

            file_put_contents($filePath, $response->getStream());

            $response->getBody()->write($filePath . $filename);
        }

        return $response;
    }
}