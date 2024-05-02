<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;

use DateTimeInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Myracloud\WebApi\Endpoint\Enum\DNSEnum;

/**
 * Class DnsRecord
 *
 * @package Myracloud\WebApi\Endpoint
 */
class DnsRecord extends AbstractEndpoint
{
    protected const ENDPOINT = 'dnsRecords';

    /**
     * @param ?string $domain
     * @param int $page
     * @param string|null $search
     * @param DNSEnum|null $recordType
     * @param bool $activeOnly
     * @param bool $loadBalancedOnly
     * @return mixed
     * @throws GuzzleException
     */
    public function getList(?string $domain = null, int $page = 1, ?string $search = null, ?DNSEnum $recordType = null, bool $activeOnly = false, bool $loadBalancedOnly = false): array
    {
        $options = [];
        if (!empty($search)) {
            $options[RequestOptions::QUERY]['search'] = $search;
        }
        if ($recordType !== null) {
            $options[RequestOptions::QUERY]['recordTypes'] = $recordType->value;
        }
        if ($activeOnly) {
            $options[RequestOptions::QUERY]['activeOnly'] = 'true';
        }
        if ($loadBalancedOnly) {
            $options[RequestOptions::QUERY]['loadbalancer'] = 'true';
        }

        return $this->handleResponse(
            $this->client->request('GET', static::ENDPOINT . '/' . $domain . '/' . $page, $options)
        );
    }

    /**
     * @param string $domain
     * @param string $subdomain
     * @param string $ipAddress
     * @param int $ttl
     * @param DNSEnum $recordType
     * @param bool $active
     * @param string $sslCertTemplate
     * @param bool $enabled
     * @return mixed
     * @throws GuzzleException
     */
    public function create(string $domain, string $subdomain, string $ipAddress, int $ttl, DNSEnum $recordType = DNSEnum::A, bool $active = true, string $sslCertTemplate = '', bool $enabled = true): array
    {
        $options[RequestOptions::JSON] =
            [
                'name'       => $subdomain,
                'value'      => $ipAddress,
                'ttl'        => $ttl,
                'recordType' => $recordType->value,
                'active'     => $active,
                'enabled'    => $enabled,
            ];
        if ($sslCertTemplate !== '') {
            $options[RequestOptions::JSON]['sslCertTemplate'] = $sslCertTemplate;
        }

        return $this->handleResponse(
            $this->client->request('PUT', static::ENDPOINT . '/' . $domain, $options)
        );
    }

    /**
     * @param string $domain
     * @param string $id
     * @param DateTimeInterface $modified
     * @param string $subdomain
     * @param string $ipAddress
     * @param int $ttl
     * @param DNSEnum $recordType
     * @param bool $active
     * @param string $sslCertTemplate
     * @param bool $enabled
     * @return array
     * @throws GuzzleException
     */
    public function update(string $domain, string $id, DateTimeInterface $modified, string $subdomain, string $ipAddress, int $ttl, DNSEnum $recordType = DNSEnum::A, bool $active = true, string $sslCertTemplate = '', bool $enabled = true): array
    {
        $options[RequestOptions::JSON] =
            [
                "id"         => $id,
                'modified'   => $modified->format(DATE_RFC3339),
                'name'       => $subdomain,
                'value'      => $ipAddress,
                'ttl'        => $ttl,
                'recordType' => $recordType->value,
                'active'     => $active,
                'enabled'    => $enabled,
            ];
        if ($sslCertTemplate !== '') {
            $options[RequestOptions::JSON]['sslCertTemplate'] = $sslCertTemplate;
        }
        return $this->handleResponse(
            $this->client->request('POST', static::ENDPOINT . '/' . $domain, $options)
        );
    }
}
