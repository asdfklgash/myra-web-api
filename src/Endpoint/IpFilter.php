<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;


use DateTimeInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Myracloud\WebApi\Endpoint\Enum\IPFilterEnum;

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
     * @param IPFilterEnum $type
     * @param string $value
     * @param bool $enabled
     * @return mixed
     * @throws GuzzleException
     */
    public function create(string $domain, IPFilterEnum $type, string $value, bool $enabled = true): array
    {
        $options[RequestOptions::JSON] =
            [
                'type'    => $type->value,
                'value'   => $value,
                'enabled' => $enabled,
            ];
        return $this->handleResponse($this->client->request('PUT', static::ENDPOINT . '/' . $domain, $options));
    }


    /**
     * @param string $domain
     * @param string $id
     * @param DateTimeInterface $modified
     * @param IPFilterEnum $type
     * @param string $value
     * @return array
     * @throws GuzzleException
     */
    public function update(string $domain, string $id, DateTimeInterface $modified, IPFilterEnum $type, string $value): array
    {
        $options[RequestOptions::JSON] =
            [
                'id'       => $id,
                'modified' => $modified->format(DATE_RFC3339),
                'type'     => $type->value,
                'value'    => $value,
            ];
        return $this->handleResponse(
            $this->client->request('POST', static::ENDPOINT . '/' . $domain, $options)
        );
    }
}
