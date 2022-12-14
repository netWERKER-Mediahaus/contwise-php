<?php

namespace Contwise\Resources;

use Contwise\Api\Connection;
use Contwise\Exceptions\ContwiseException;

/**
 * Class AbstractResource.
 *
 * @namespace    Contwise\Resources
 * @author     Mauthi <mauthi@gmail.com>
 */
abstract class AbstractResource
{
    const SUCCESS = 'success';
    protected Connection $connection;
    protected String $resourceUrl;

    /**
     * AbstractResource constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection, String $resourceUrl)
    {
        $this->connection = $connection;
        $this->resourceUrl = $resourceUrl;
    }

    protected function filterResult($result)
    {
        // array_walk_recursive($result, function (&$value) {
        //     $value = htmlspecialchars_decode($value);
        // });

        return $result;
    }

    protected function getResult(array $result, String $key) :?array
    {
        if (isset($result[$key])) {
            return $result[$key];
        }

        return null;
    }

    protected function multipartRequest(String $url, array $data, array $options = [])
    {
        $options['multipart'] = $data;
        return $this->postRequest($url, $options);
    }

    protected function jsonRequest(String $url, array $data, array $options = [])
    {
        $options['json'] = $data;
        $options['headers']['Content-Type'] = 'application/json';
        return $this->postRequest($url, $options);
    }

    private function postRequest(String $url, array $options = [])
    {
        return $this->request('POST', $url, $options);
    }

    private function request(String $method, String $url, array $options = []) :array
    {
        return $this->connection->request($method, $url, $options);
    }

    private function getResultSize($response) :?int
    {
        if (isset($response['properties']['totalSize'])) {
            return $response['properties']['totalSize'];
        }

        return null;
    }

    protected function checkIfExactOneResult($response)
    {
        if ($this->getResultSize($response) !== 1) {
            throw new ContwiseException("0 or more than 1 results found\nResponse: ".print_r($response, true));
        }
    }
}
