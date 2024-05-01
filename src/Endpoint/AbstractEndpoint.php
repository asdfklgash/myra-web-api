<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;

use DateTime;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Utils;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AbstractEndpoint
 *
 * @package Myracloud\WebApi\Endpoint
 */
abstract class AbstractEndpoint
{
    /**
     * HTTP 301
     */
    public const REDIRECT_TYPE_PERMANENT = 'permanent';
    /**
     * HTTP 302
     */
    public const REDIRECT_TYPE_REDIRECT = 'redirect';

    public const MATCHING_TYPE_PREFIX = 'prefix';
    public const MATCHING_TYPE_SUFFIX = 'suffix';
    public const MATCHING_TYPE_EXACT  = 'exact';

    public const DNS_TYPE_A     = 'A';
    public const DNS_TYPE_AAAA  = 'AAAA';
    public const DNS_TYPE_MX    = 'MX';
    public const DNS_TYPE_CNAME = 'CNAME';
    public const DNS_TYPE_TXT   = 'TXT';
    public const DNS_TYPE_NS    = 'NS';
    public const DNS_TYPE_SRV   = 'SRV';
    public const DNS_TYPE_CAA   = 'CAA';

    public const IPFILTER_TYPE_WHITELIST = 'WHITELIST';
    public const IPFILTER_TYPE_BLACKLIST = 'BLACKLIST';

    protected const ENDPOINT = '';

    /**
     * Domain constructor.
     *
     * @param Client $client
     * @throws Exception
     */
    public function __construct(
        protected readonly Client $client
    ) {
        if (static::ENDPOINT === '') {
            throw new Exception('Must define endpoint in '. static::class .'::ENDPOINT');
        }
    }

    /**
     * @param string $domain
     * @param string $id
     * @param DateTime $modified
     * @return array
     * @throws GuzzleException
     */
    public function delete(string $domain, string $id, DateTime $modified): array
    {
        $options[RequestOptions::JSON] =
            [
                'id'       => $id,
                'modified' => $modified->format('c'),
            ];

        return $this->handleResponse(
            $this->client->request('DELETE', static::ENDPOINT . '/' . $domain, $options)
        );
    }

    /**
     * @param ?string $domain
     * @param int $page
     * @return array
     * @throws GuzzleException
     */
    public function getList(?string $domain = null, int $page = 1): array
    {
        return $this->handleResponse(
            $this->client->get(
                static::ENDPOINT . '/' . $domain . '/' . $page
            )
        );
    }

    public function getEndPoint(): string
    {
        return static::ENDPOINT;
    }

    /**
     * @param string $value
     * @throws Exception
     */
    protected function validateMatchingType(string $value): void
    {
        if (!in_array($value, [
            self::MATCHING_TYPE_EXACT,
            self::MATCHING_TYPE_PREFIX,
            self::MATCHING_TYPE_SUFFIX,
        ])) {
            throw new Exception('Unknown Matching Type.');
        }

    }

    /**
     * @param string $value
     * @throws Exception
     */
    protected function validateRedirectType(string $value): void
    {
        if (!in_array($value, [
            self::REDIRECT_TYPE_PERMANENT,
            self::REDIRECT_TYPE_REDIRECT,
        ])) {
            throw new Exception('Unknown Redirect Type.');
        }
    }

    /**
     * @param string $value
     * @throws Exception
     */
    protected function validateDnsType(string $value): void
    {
        if (!in_array($value, [
            self::DNS_TYPE_A,
            self::DNS_TYPE_AAAA,
            self::DNS_TYPE_MX,
            self::DNS_TYPE_CNAME,
            self::DNS_TYPE_TXT,
            self::DNS_TYPE_NS,
            self::DNS_TYPE_SRV,
            self::DNS_TYPE_CAA,
        ])) {
            throw new Exception('Unknown Record Type.');
        }
    }

    /**
     * @param string $value
     * @throws Exception
     */
    protected function validateIpfilterType(string $value): void
    {
        if (!in_array($value, [
            self::IPFILTER_TYPE_BLACKLIST,
            self::IPFILTER_TYPE_WHITELIST,
        ])) {
            throw new Exception('Unknown IpFilter Type.');
        }
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    protected function handleResponse(ResponseInterface $response): array
    {
        if ($response->getStatusCode() != 200) {
            throw new TransferException(
                'Invalid Response. ' . $response->getStatusCode() . ' ' . $response->getReasonPhrase()
            );
        }

        return Utils::jsonDecode($response->getBody()->getContents(), true);
    }
}
