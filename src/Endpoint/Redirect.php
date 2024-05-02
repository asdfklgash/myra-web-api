<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;

use DateTimeInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Myracloud\WebApi\Endpoint\Enum\MatchEnum;
use Myracloud\WebApi\Endpoint\Enum\RedirectEnum;

/**
 * Class Redirect
 *
 * @package Myracloud\WebApi\Endpoint
 */
class Redirect extends AbstractEndpoint
{
    /**
     * @var string
     */
    protected const ENDPOINT = 'redirects';

    /**
     * @param string $domain
     * @param string $source
     * @param string $destination
     * @param RedirectEnum $type
     * @param MatchEnum $matchingType
     * @param bool $expertMode
     * @return array
     * @throws GuzzleException
     */
    public function create(string $domain, string $source, string $destination, RedirectEnum $type = RedirectEnum::Redirect, MatchEnum $matchingType = MatchEnum::Prefix, bool $expertMode = false): array
    {
        $options[RequestOptions::JSON] =
            [
                "source"       => $source,
                "destination"  => $destination,
                "type"         => $type->value,
                "matchingType" => $matchingType->value,
                "expertMode"   => $expertMode,
            ];
        return $this->handleResponse($this->client->request('PUT', static::ENDPOINT . '/' . $domain, $options));
    }

    /**
     * @param string $domain
     * @param string $id
     * @param DateTimeInterface $modified
     * @param string $source
     * @param string $destination
     * @param RedirectEnum $type
     * @param MatchEnum $matchingType
     * @param bool $expertMode
     * @return array
     * @throws GuzzleException
     */
    public function update(string $domain, string $id, DateTimeInterface $modified, string $source, string $destination, RedirectEnum $type = RedirectEnum::Redirect, MatchEnum $matchingType = MatchEnum::Prefix, bool $expertMode = false): array
    {
        $options[RequestOptions::JSON] =
            [
                "id"           => $id,
                'modified'     => $modified->format(DATE_RFC3339),
                "source"       => $source,
                "destination"  => $destination,
                "type"         => $type->value,
                "matchingType" => $matchingType->value,
                "expertMode"   => $expertMode,
            ];
        return $this->handleResponse(
            $this->client->request('POST', static::ENDPOINT . '/' . $domain, $options)
        );
    }
}
