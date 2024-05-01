<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;

use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

/**
 * Class CacheSetting
 *
 * @package Myracloud\WebApi\Endpoint
 */
class CacheSetting extends AbstractEndpoint
{
    protected const ENDPOINT = 'cacheSettings';

    /**
     * @param string $domain
     * @param string $path
     * @param int $ttl
     * @param string $type
     * @param bool $enabled
     * @return array
     * @throws GuzzleException
     * @throws Exception
     */
    public function create(string $domain, string $path, int $ttl, string $type = self::MATCHING_TYPE_PREFIX, bool $enabled = true): array
    {
        $this->validateMatchingType($type);
        $options[RequestOptions::JSON] =
            [
                "path"    => $path,
                "ttl"     => $ttl,
                "type"    => $type,
                "enabled" => $enabled,
            ];
        return $this->handleResponse(
            $this->client->request('PUT', static::ENDPOINT . '/' . $domain, $options)
        );
    }

    /**
     * @param string $domain
     * @param string $id
     * @param DateTime $modified
     * @param string $path
     * @param int $ttl
     * @param string $type
     * @param bool $enabled
     * @return array
     * @throws GuzzleException
     * @throws Exception
     */
    public function update(string $domain, string $id, DateTime $modified, string $path, int $ttl, string $type = self::MATCHING_TYPE_PREFIX, bool $enabled = true): array
    {
        $this->validateMatchingType($type);
        $options[RequestOptions::JSON] =
            [
                "id"       => $id,
                'modified' => $modified->format('c'),
                "path"     => $path,
                "ttl"      => $ttl,
                "type"     => $type,
                "enabled"  => $enabled,
            ];
        return $this->handleResponse(
            $this->client->request('POST', static::ENDPOINT . '/' . $domain, $options)
        );
    }
}
