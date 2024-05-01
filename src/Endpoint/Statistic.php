<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;

use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

/**
 * Class Statistic
 *
 * @package Myracloud\WebApi\Endpoint
 */
class Statistic extends AbstractEndpoint
{
    /**
     * @var string
     */
    protected const ENDPOINT = 'statistic';

    /**
     * @param string $domain
     * @param string $id
     * @param DateTime $modified
     * @return array
     * @throws Exception
     */
    public function delete(string $domain, string $id, DateTime $modified): array
    {
        throw new Exception('Delete is not supported on ' . __CLASS__);
    }

    /**
     * @param array $query
     * @return array
     * @throws GuzzleException
     */
    public function query(array $query): array
    {
        $options[RequestOptions::JSON] = $query;
        return $this->handleResponse(
            $this->client->request('POST', static::ENDPOINT . '/query', $options)
        );
    }
}
