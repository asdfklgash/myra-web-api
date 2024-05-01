<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;

use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

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
     * @param string $type
     * @param string $matchingType
     * @param bool $expertMode
     * @return array
     * @throws GuzzleException
     * @throws Exception
     */
    public function create(string $domain, string $source, string $destination, string $type = self::REDIRECT_TYPE_REDIRECT, string $matchingType = self::MATCHING_TYPE_PREFIX, bool $expertMode = false): array
    {
        $this->validateRedirectType($type);
        $this->validateMatchingType($matchingType);
        $options[RequestOptions::JSON] =
            [
                "source"       => $source,
                "destination"  => $destination,
                "type"         => $type,
                "matchingType" => $matchingType,
                "expertMode"   => $expertMode,
            ];
        return $this->handleResponse($this->client->request('PUT', static::ENDPOINT . '/' . $domain, $options));
    }

    /**
     * @param string $domain
     * @param string $id
     * @param DateTime $modified
     * @param string $source
     * @param string $destination
     * @param string $type
     * @param string $matchingType
     * @param bool $expertMode
     * @return array
     * @throws GuzzleException
     * @throws Exception
     */
    public function update(string $domain, string $id, DateTime $modified, string $source, string $destination, string $type = self::REDIRECT_TYPE_REDIRECT, string $matchingType = self::MATCHING_TYPE_PREFIX, bool $expertMode = false): array
    {
        $this->validateRedirectType($type);
        $this->validateMatchingType($matchingType);
        $options[RequestOptions::JSON] =
            [
                "id"           => $id,
                'modified'     => $modified->format('c'),
                "source"       => $source,
                "destination"  => $destination,
                "type"         => $type,
                "matchingType" => $matchingType,
                "expertMode"   => $expertMode,
            ];
        return $this->handleResponse(
            $this->client->request('POST', static::ENDPOINT . '/' . $domain, $options)
        );
    }
}
