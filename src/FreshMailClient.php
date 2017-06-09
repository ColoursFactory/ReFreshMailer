<?php

namespace ColoursFactory\ReFreshMailer;

use ColoursFactory\ReFreshMailer\Exception\FreshMailApiErrorException;
use ColoursFactory\ReFreshMailer\Exception\FreshMailApiException;
use ColoursFactory\ReFreshMailer\Exception\HttpAdapterException;
use ColoursFactory\ReFreshMailer\HttpAdapter\HttpAdapterInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Konrad Pawlikowski <preclowski@gmail.com>
 * @link https://github.com/Preclowski/ReFreshMailer
 * @license MIT
 */
class FreshMailClient
{
    /** @var string */
    private $host;
    /** @var HttpAdapterInterface */
    private $httpAdapter;
    /** @var string */
    private $apiKey;
    /** @var string */
    private $apiSecret;

    /**
     * @param HttpAdapterInterface $httpAdapter
     * @param string $host
     * @param string $apiKey
     * @param string $apiSecret
     */
    public function __construct(HttpAdapterInterface $httpAdapter, $host, $apiKey, $apiSecret)
    {
        $this->httpAdapter = $httpAdapter;
        $this->host = $host;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    /**
     * @param string $url
     * @param array $params
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     * @throws \ColoursFactory\ReFreshMailer\Exception\FreshMailApiErrorException
     * @throws \ColoursFactory\ReFreshMailer\Exception\FreshMailApiException
     */
    public function doRequest($url, array $params = [])
    {
        $headers = [
            'X-Rest-ApiKey' => $this->apiKey,
            'X-Rest-ApiSign' => $this->calculateSignature($url, $params),
            'Content-Type' => 'application/json',
        ];

        $address = sprintf('%s/%s', $this->host, $url);
        $method = empty($params) ? 'GET' : 'POST';

        try {
            $response = $this->httpAdapter->sendRequest($method, $address, $params, $headers);
        } catch (HttpAdapterException $e) {
            throw new FreshMailApiException($e->getMessage(), $e->getCode(), $e);
        }

        return $this->handleResponse($response, $url);
    }

    /**
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function calculateSignature($url, $params)
    {
        return sha1(
            $this->apiKey .
            $url .
            json_encode($params) .
            $this->apiSecret
        );
    }

    /**
     * @param ResponseInterface $response
     * @param string $url
     *
     * @return array
     *
     * @throws \ColoursFactory\ReFreshMailer\Exception\FreshMailApiErrorException
     * @throws \ColoursFactory\ReFreshMailer\Exception\FreshMailApiException
     */
    private function handleResponse(ResponseInterface $response, $url)
    {
        $contentType = $response->getHeader('Content-Type');

        if ('application/zip' === $contentType) {
            $this->handleFileResponse($response);
        }

        try {
            $content = \GuzzleHttp\json_decode($response->getStream());
        } catch (\InvalidArgumentException $e) {
            throw new FreshMailApiException($e->getMessage(), $e->getCode(), $e);
        }

        if (200 !== $response->getStatusCode()) {
            throw new FreshMailApiErrorException($url, $content['errors']);
        }

        return $content;
    }

    /**
     * Saves file to directory specified in configuration
     *
     * @param ResponseInterface $response
     */
    private function handleFileResponse(ResponseInterface $response)
    {
        $filename = $response->getHeader('Filename');

        file_put_contents($this->getFilePath($filename), $response->getStream());
    }

    /**
     * Returns absolute path to file
     *
     * @param string $filename
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function getFilePath($filename)
    {
        return rtrim('/tmp', '/') . DIRECTORY_SEPARATOR . $filename;
    }
}