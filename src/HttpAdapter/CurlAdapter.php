<?php

namespace Preclowski\ReFreshMailer\HttpAdapter;

use Preclowski\ReFreshMailer\Message\BufferStream;
use Preclowski\ReFreshMailer\Message\CurlResponse;

/**
 * Class implements basic cURL featrues in order to make
 * API requests when we cannot eg. install Guzzle.
 *
 * @author Konrad Pawlikowski <preclowski@gmail.com>
 * @link https://github.com/Preclowski/ReFreshMailer
 * @license MIT
 */
class CurlAdapter implements HttpAdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function sendRequest($method, $url, array $params = [], array $headers = [])
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if (!empty($params)) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        }

        $body       = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $headers    = substr($body, 0, curl_getinfo($curl, CURLINFO_HEADER_SIZE));

        $stream = new BufferStream();
        $stream->write($body);

        return new CurlResponse($statusCode, $headers, $stream);
    }
}