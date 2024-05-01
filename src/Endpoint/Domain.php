<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;

use DateTime;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

/**
 * Class Domain
 *
 * @package Myracloud\WebApi\Endpoint
 */
class Domain extends AbstractEndpoint
{
    protected const ENDPOINT = 'domains';

    /**
     * @param ?string $domain
     * @param int $page
     * @return array
     * @throws GuzzleException
     */
    public function getList(?string $domain = null, int $page = 1): array
    {
        return $this->handleResponse(
            $this->client->get(static::ENDPOINT . '/' . $page)
        );
    }

    /**
     * @param string $name
     * @param bool $autoUpdate
     * @return array
     * @throws GuzzleException
     */
    public function create(string $name, bool $autoUpdate = false): array
    {
        $options[RequestOptions::JSON] =
            [
                'name'       => $name,
                'autoUpdate' => $autoUpdate,
            ];
        return $this->handleResponse($this->client->request('PUT', static::ENDPOINT, $options));
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
                'name'     => $domain,
                'id'       => $id,
                'modified' => $modified->format('c'),
            ];

        return $this->handleResponse(
            $this->client->request('DELETE', static::ENDPOINT, $options)
        );
    }

    /**
     * @param string $id
     * @param DateTime $modified
     * @param bool $autoUpdate
     * @return array
     * @throws GuzzleException
     */
    public function update(string $id, DateTime $modified, bool $autoUpdate = false): array
    {
        $options[RequestOptions::JSON] =
            [
                'id'         => $id,
                'modified'   => $modified->format('c'),
                'autoUpdate' => $autoUpdate,
            ];
        return $this->handleResponse(
            $this->client->request('POST', static::ENDPOINT, $options)
        );
    }
}
