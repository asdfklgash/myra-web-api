<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;

use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

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
     * @param string|null $recordType
     * @param bool $activeOnly
     * @param bool $loadBalancedOnly
     * @return mixed
     * @throws GuzzleException
     * @throws Exception
     */
    public function getList(?string $domain = null, int $page = 1, ?string $search = null, ?string $recordType = null, bool $activeOnly = false, bool $loadBalancedOnly = false): array
    {
        $options = [];
        if (!empty($search)) {
            $options[RequestOptions::QUERY]['search'] = $search;
        }
        if (!empty($recordType)) {
            $this->validateDnsType($recordType);
            $options[RequestOptions::QUERY]['recordTypes'] = $recordType;
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
     * @param string $recordType
     * @param bool $active
     * @param string $sslCertTemplate
     * @param bool $enabled
     * @return mixed
     * @throws GuzzleException
     * @throws Exception
     */
    public function create(string $domain, string $subdomain, string $ipAddress, int $ttl, string $recordType = self::DNS_TYPE_A, bool $active = true, string $sslCertTemplate = '', bool $enabled = true): array
    {
        $this->validateDnsType($recordType);
        $options[RequestOptions::JSON] =
            [
                'name'       => $subdomain,
                'value'      => $ipAddress,
                'ttl'        => $ttl,
                'recordType' => $recordType,
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
     * @param DateTime $modified
     * @param string $subdomain
     * @param string $ipAddress
     * @param int $ttl
     * @param string $recordType
     * @param bool $active
     * @param string $sslCertTemplate
     * @param bool $enabled
     * @return array
     * @throws GuzzleException
     * @throws Exception
     */
    public function update(string $domain, string $id, DateTime $modified, string $subdomain, string $ipAddress, int $ttl, string $recordType = self::DNS_TYPE_A, bool $active = true, string $sslCertTemplate = '', bool $enabled = true): array
    {
        $this->validateDnsType($recordType);
        $options[RequestOptions::JSON] =
            [
                "id"         => $id,
                'modified'   => $modified->format('c'),
                'name'       => $subdomain,
                'value'      => $ipAddress,
                'ttl'        => $ttl,
                'recordType' => $recordType,
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
