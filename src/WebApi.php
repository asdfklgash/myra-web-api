<?php
declare(strict_types=1);

namespace Myracloud\WebApi;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Myracloud\WebApi\Endpoint\CacheClear;
use Myracloud\WebApi\Endpoint\CacheSetting;
use Myracloud\WebApi\Endpoint\Certificate;
use Myracloud\WebApi\Endpoint\DnsRecord;
use Myracloud\WebApi\Endpoint\Domain;
use Myracloud\WebApi\Endpoint\IpFilter;
use Myracloud\WebApi\Endpoint\Maintenance;
use Myracloud\WebApi\Endpoint\Networks;
use Myracloud\WebApi\Endpoint\Redirect;
use Myracloud\WebApi\Endpoint\Statistic;
use Myracloud\WebApi\Endpoint\SubdomainSetting;
use Myracloud\WebApi\Middleware\Signature;

/**
 * Class WebApi
 *
 * @package Myracloud\WebApi
 */
class WebApi
{
    private const DEFAULT_API_DOMAIN = 'api.myracloud.com';
    private const DEFAULT_API_LANG = 'en';
    /**
     * @var Client
     */
    protected Client $client;
    /**
     * @var array
     */
    private array $endpointCache = [];

    /**
     * @param string $apiKey
     * @param string $secret
     * @param string $site
     * @param string $lang
     * @param array $connectionConfig
     * @param callable|null $requestHandler default is CurlHandler
     */
    public function __construct(
        string $apiKey,
        string $secret,
        string $site = self::DEFAULT_API_DOMAIN,
        string $lang = self::DEFAULT_API_LANG,
        array $connectionConfig = [],
        ?callable $requestHandler = null
    ) {
        if (!is_callable($requestHandler))
            $requestHandler = new CurlHandler();

        $stack = HandlerStack::create($requestHandler);
        $signature = new Signature($secret, $apiKey);
        $stack->push(
            Middleware::mapRequest(
                $signature->signRequest(...)
            )
        );

        $client = new Client(
            $connectionConfig + [
                'base_uri' => 'https://' . $site . '/' . $lang . '/rapi/',
                'handler'  => $stack,
            ]
        );
        $this->client = $client;
    }

    /**
     * @template T of object
     * @param class-string<T> $className
     * @return T the created instance
     */
    private function getInstance(string $className)
    {
        return $this->endpointCache[$className] ??= new $className($this->client);
    }

    public function getDomainEndpoint(): Domain
    {
        return $this->getInstance(Domain::class);
    }

    public function getRedirectEndpoint(): Redirect
    {
        return $this->getInstance(Redirect::class);
    }

    public function getCacheSettingsEndpoint(): CacheSetting
    {
        return $this->getInstance(CacheSetting::class);
    }

    public function getCertificateEndpoint(): Certificate
    {
        return $this->getInstance(Certificate::class);
    }

    public function getSubdomainSettingsEndpoint(): SubdomainSetting
    {
        return $this->getInstance(SubdomainSetting::class);
    }

    public function getDnsRecordEndpoint(): DnsRecord
    {
        return $this->getInstance(DnsRecord::class);
    }

    public function getStatisticEndpoint(): Statistic
    {
        return $this->getInstance(Statistic::class);
    }

    public function getMaintenanceEndpoint(): Maintenance
    {
        return $this->getInstance(Maintenance::class);
    }

    public function getIpFilterEndpoint(): IpFilter
    {
        return $this->getInstance(IpFilter::class);
    }

    public function getCacheClearEndpoint(): CacheClear
    {
        return $this->getInstance(CacheClear::class);
    }

    public function getNetworksEndpoint(): Networks
    {
        return $this->getInstance(Networks::class);
    }
}
