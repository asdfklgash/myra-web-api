<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

/**
 * Class SubdomainSetting
 *
 * @package Myracloud\WebApi\Endpoint
 */
class Bind extends AbstractEndpoint
{
    /**
     * @var string
     */
    protected const ENDPOINT = 'bind';


    /**
     * @param string $domain
     * @return array
     * @throws GuzzleException
     */
    public function get(string $domain): array
    {
        return $this->handleResponse(
            $this->client->request('GET', static::ENDPOINT . '/' . $domain)
        );
    }

}
