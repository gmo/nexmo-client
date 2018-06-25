<?php

namespace Nexmo;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Uri;
use Nexmo\Service;
use Nexmo\Service\ResourceCollection;
use Psr\Http\Message\RequestInterface;

/**
 * Class Client
 *
 * @property-read Service\Account $account Account management APIs
 * @property-read Service\Message $message
 * @property-read Service\Voice   $voice
 * @property-read Service\Verify  $verify
 *
 * @package Nexmo\Client
 */
class Client extends ResourceCollection
{
    const BASE_URL = 'https://rest.nexmo.com/';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
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

    protected function getNamespace()
    {
        return 'Nexmo\Service';
    }

    public function __get($name)
    {
        $this->loadClient();

        return parent::__get($name);
    }

    protected function loadClient()
    {
        if ($this->client) {
            return;
        }

        $handler = new CurlHandler();
        $stack = HandlerStack::create($handler);
        $stack->push($this->getDefaultsMiddleware());

        $this->client = new HttpClient([
            'base_uri' => static::BASE_URL,
            'handler'  => $stack,
        ]);

    }

    protected function getDefaultsMiddleware()
    {
        return function(RequestInterface $request) {
            $uri = Uri::withQueryValue($request->getUri(), 'api_key', $this->apiKey);
            $uri = Uri::withQueryValue($uri, 'api_secret', $this->apiSecret);
            return $request->withUri($uri);
        };
    }
}
