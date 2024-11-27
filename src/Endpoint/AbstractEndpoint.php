<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;

use DateTimeInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Utils;
use Myracloud\WebApi\Exception\EndpointException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AbstractEndpoint
 *
 * @package Myracloud\WebApi\Endpoint
 */
abstract class AbstractEndpoint
{
    protected const ENDPOINT = '';

    /**
     * Domain constructor.
     *
     * @param Client $client
     * @throws EndpointException
     */
    public function __construct(
        protected readonly Client $client
    ) {
        if (static::ENDPOINT === '') {
            throw new EndpointException('Must define endpoint in '. static::class .'::ENDPOINT');
        }
    }

    /**
     * @param string $domain
     * @param string $id
     * @param DateTimeInterface $modified
     * @return array
     * @throws GuzzleException
     */
    public function delete(string $domain, string $id, DateTimeInterface $modified): array
    {
        $options[RequestOptions::JSON] =
            [
                'id'       => $id,
                'modified' => $modified->format(DATE_RFC3339),
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

    /**
     * @param ResponseInterface $response
     * @return array
     */
    protected function handleRawResponse(ResponseInterface $response): string
    {
        if ($response->getStatusCode() != 200) {
            throw new TransferException(
                'Invalid Response. ' . $response->getStatusCode() . ' ' . $response->getReasonPhrase()
            );
        }

        return $response->getBody()->getContents();
    }
}
