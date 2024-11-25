<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Utils;
use Psr\Http\Message\ResponseInterface;

/**
 * Class SubdomainSetting
 *
 * @package Myracloud\WebApi\Endpoint
 */
class BindRaw extends AbstractEndpoint
{
    /**
     * @var string
     */
    protected const ENDPOINT = 'bindRaw';


    /**
     * @param string $domain
     * @return array
     * @throws GuzzleException
     */
    public function get(string $domain): string
    {
        return $this->handleTextResponse(
            $this->client->request('GET', static::ENDPOINT . '/' . $domain)
        );
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    protected function handleTextResponse(ResponseInterface $response): string
    {
        if ($response->getStatusCode() != 200) {
            throw new TransferException(
                'Invalid Response. ' . $response->getStatusCode() . ' ' . $response->getReasonPhrase()
            );
        }

        return $response->getBody()->getContents();
    }

}
