<?php

namespace Preclowski\ReFreshMailer\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * This interface must be implemented in every HttpAdapter
 * implementation
 *
 * @author Konrad Pawlikowski <preclowski@gmail.com>
 * @link https://github.com/Preclowski/ReFreshMailer
 * @license MIT
 */
interface HttpAdapterInterface
{
    /**
     * @param $method
     * @param string $url
     * @param array $params
     * @param array $headers
     * @return ResponseInterface
     */
    public function doRequest($method, $url, array $params = [], array $headers = []);
}