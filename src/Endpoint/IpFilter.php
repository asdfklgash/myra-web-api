<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;


use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

/**
 * Class IpFilter
 *
 * @package Myracloud\WebApi\Endpoint
 */
class IpFilter extends AbstractEndpoint
{
    /**
     * @var string
     */
    protected const ENDPOINT = 'ipfilter';

    /**
     * @param string $domain
     * @param string $type
     * @param string $value
     * @param bool $enabled
     * @return mixed
     * @throws GuzzleException
     * @throws Exception
     */
    public function create(string $domain, string $type, string $value, bool $enabled = true): array
    {
        $this->validateIpfilterType($type);
        $options[RequestOptions::JSON] =
            [
                'type'    => $type,
                'value'   => $value,
                'enabled' => $enabled,
            ];
        return $this->handleResponse($this->client->request('PUT', static::ENDPOINT . '/' . $domain, $options));
    }


    /**
     * @param string $domain
     * @param string $id
     * @param DateTime $modified
     * @param string $type
     * @param string $value
     * @return array
     * @throws GuzzleException
     * @throws Exception
     */
    public function update(string $domain, string $id, DateTime $modified, string $type, string $value): array
    {
        $this->validateIpfilterType($type);
        $options[RequestOptions::JSON] =
            [
                'id'       => $id,
                'modified' => $modified->format('c'),
                'type'     => $type,
                'value'    => $value,
            ];
        return $this->handleResponse(
            $this->client->request('POST', static::ENDPOINT . '/' . $domain, $options)
        );
    }
}
