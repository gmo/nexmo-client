<?php

namespace Nexmo\Service;

use Nexmo\Exception;

/**
 * Class Service
 * @package Nexmo\Service
 */
abstract class Service extends Resource
{
    /**
     * @return string
     */
    abstract public function getEndpoint();

    /**
     * @return mixed
     */
    abstract public function invoke();

    /**
     * @param array $json
     * @return bool
     */
    abstract protected function validateResponse(array $json);

    /**
     * @param $params
     * @throws Exception
     * @return array
     */
    protected function exec($params)
    {
        $params = array_filter($params);

        $response = $this->client->request('get', $this->getEndpoint(), [
            'query' => $params
        ]);

        $json = json_decode($response->getBody(), true);
        if (is_null($json)) {
            throw new Exception('Invalid JSON', 0);
        }

        $this->validateResponse($json);

        return $json;
    }

    /**
     * @return array
     */
    public function __invoke()
    {
        return call_user_func_array(array($this, 'invoke'), func_get_args());
    }
}
