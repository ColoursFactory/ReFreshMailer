<?php

namespace ColoursFactory\ReFreshMailer\HttpClient;

use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;

class HttpClientFactory
{
    public static function create(array $plugins = [], HttpClient $client = null)
    {
        $client = $client ?: HttpClientDiscovery::find();

        $plugins[] = new ErrorPlugin();

        return new PluginClient($client, $plugins);
    }
}