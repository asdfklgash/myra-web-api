<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;

use DateTime;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

/**
 * Class Maintenance
 *
 * @package Myracloud\WebApi\Endpoint
 */
class Maintenance extends AbstractEndpoint
{
    /**
     * @var string
     */
    protected const ENDPOINT = 'maintenance';


    /**
     * @param string $domain
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param string|null $content
     * @return mixed
     * @throws GuzzleException
     */
    public function create(string$domain, DateTime $startDate, DateTime $endDate, ?string $content = null): array
    {
        $options[RequestOptions::JSON] =
            [
                'content' => $content,
                'start'   => $startDate->format('c'),
                'end'     => $endDate->format('c'),
            ];
        return $this->handleResponse(
            $this->client->request('PUT', static::ENDPOINT . '/' . $domain, $options)
        );
    }

    /**
     * @param string $domain
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param string $customLabel
     * @param string $customUrl
     * @param string $facebookUrl
     * @param string $twitterUrl
     * @return array
     * @throws GuzzleException
     */
    public function createDefaultPage(string $domain, DateTime $startDate, DateTime $endDate, string $customLabel = '', string $customUrl = '', string $facebookUrl = '', string $twitterUrl = ''): array
    {
        $pageData = [];
        if ($facebookUrl !== '') {
            $pageData['facebook'] = $facebookUrl;
        }
        if ($twitterUrl !== '') {
            $pageData['twitter'] = $twitterUrl;
        }
        if ($customLabel !== '') {
            $pageData['custom']['label'] = $customLabel;
        }
        if ($customUrl !== '') {
            $pageData['custom']['url'] = $customUrl;
        }

        $options[RequestOptions::JSON] =
            [
                'start'       => $startDate->format('c'),
                'end'         => $endDate->format('c'),
                'defaultPage' => $pageData,
            ];

        return $this->handleResponse(
            $this->client->request('PUT', static::ENDPOINT . '/' . $domain, $options)
        );
    }

    /**
     * @param string $domain
     * @param string $id
     * @param DateTime $modified
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param string|null $content
     * @return array
     * @throws GuzzleException
     */
    public function update(string $domain, string $id, DateTime $modified, DateTime $startDate, DateTime $endDate, ?string $content = null): array
    {
        $options[RequestOptions::JSON] =
            [
                'id'       => $id,
                'modified' => $modified->format('c'),
                'content'  => $content,
                'start'    => $startDate->format('c'),
                'end'      => $endDate->format('c'),
            ];
        return $this->handleResponse(
            $this->client->request('POST', static::ENDPOINT . '/' . $domain, $options)
        );
    }
}
