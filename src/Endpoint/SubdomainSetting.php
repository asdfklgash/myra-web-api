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
class SubdomainSetting extends AbstractEndpoint
{
    /**
     * @var string
     */
    protected const ENDPOINT = 'subdomainSetting';


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

    /**
     * @param string $domain
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    public function set(string $domain, array $data): array
    {
        $options[RequestOptions::JSON] = $data;
        return $this->handleResponse(
            $this->client->request('POST', static::ENDPOINT . '/' . $domain, $options)
        );
    }


}
