<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;

use GuzzleHttp\Exception\GuzzleException;

/**
 * Class Networks
 *
 * @package Myracloud\WebApi\Endpoint
 */
class Networks extends AbstractEndpoint
{
    /**
     * @var string
     */
    protected const ENDPOINT = 'networks';

    /**
     * @param string|null $domain
     * @param int $page
     * @param array $params
     * @return array
     * @throws GuzzleException
     */
    public function getList(?string $domain = null, int $page = 1, array $params = []): array
    {
        return $this->handleResponse($this->client->get(static::ENDPOINT . '/' . $page, ['query' => $params]));
    }
}
