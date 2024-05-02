<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Myracloud\WebApi\Endpoint\Enum\ObjectVOEnum;

/**
 * Class Certificate
 *
 * @package Myracloud\WebApi\Endpoint
 */
class Certificate extends AbstractEndpoint
{
    protected const ENDPOINT = 'certificates';

    /**
     * @param string $domain
     * @param ObjectVOEnum $objectType
     * @param string $cert
     * @param string $key
     * @return array
     * @throws GuzzleException
     */
    public function create(string $domain, ObjectVOEnum $objectType = ObjectVOEnum::SslCertVO, string $cert = '', string $key = ''): array
    {
        $options[RequestOptions::JSON] =
            [
                'objectType' => $objectType->value,
                'cert'       => $cert,
                'key'        => $key,
            ];
        return $this->handleResponse(
            $this->client->request('PUT', static::ENDPOINT . '/' . $domain, $options)
        );
    }

}
