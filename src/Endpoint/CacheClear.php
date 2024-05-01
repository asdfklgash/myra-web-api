<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

/**
 * Class CacheClear
 *
 * @package Myracloud\WebApi\Endpoint
 */
class CacheClear extends AbstractEndpoint
{
    protected const ENDPOINT = 'cacheClear';

    /**
     * @param string $domain
     * @param string $fqdn
     * @param string $resource
     * @param bool $recursive
     * @return array
     * @throws GuzzleException
     */
    public function clear(string $domain, string $fqdn, string $resource, bool $recursive = false): array
    {
        $options[RequestOptions::JSON] =
            [
                "fqdn"      => $fqdn,
                "resource"  => $resource,
                "recursive" => $recursive,
            ];
        return $this->handleResponse(
            $this->client->request('PUT', static::ENDPOINT . '/' . $domain, $options)
        );
    }
}
