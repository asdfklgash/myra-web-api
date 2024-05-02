<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;

use DateTimeInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Myracloud\WebApi\Endpoint\Enum\MatchEnum;

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
     * @param MatchEnum $type
     * @param bool $enabled
     * @return array
     * @throws GuzzleException
     */
    public function create(string $domain, string $path, int $ttl, MatchEnum $type = MatchEnum::Prefix, bool $enabled = true): array
    {
        $options[RequestOptions::JSON] =
            [
                "path"    => $path,
                "ttl"     => $ttl,
                "type"    => $type->value,
                "enabled" => $enabled,
            ];
        return $this->handleResponse(
            $this->client->request('PUT', static::ENDPOINT . '/' . $domain, $options)
        );
    }

    /**
     * @param string $domain
     * @param string $id
     * @param DateTimeInterface $modified
     * @param string $path
     * @param int $ttl
     * @param MatchEnum $type
     * @param bool $enabled
     * @return array
     * @throws GuzzleException
     */
    public function update(string $domain, string $id, DateTimeInterface $modified, string $path, int $ttl, MatchEnum $type = MatchEnum::Prefix, bool $enabled = true): array
    {
        $options[RequestOptions::JSON] =
            [
                "id"       => $id,
                'modified' => $modified->format(DATE_RFC3339),
                "path"     => $path,
                "ttl"      => $ttl,
                "type"     => $type->value,
                "enabled"  => $enabled,
            ];
        return $this->handleResponse(
            $this->client->request('POST', static::ENDPOINT . '/' . $domain, $options)
        );
    }
}
