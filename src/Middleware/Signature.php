<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Middleware;

use Psr\Http\Message\RequestInterface;

/**
 * Class Signature
 *
 * @package Myracloud\WebApi\Authentication
 */
class Signature
{
    private string $date;
    private const CONTENT_TYPE = 'application/json';
    private const SIG_KEY = 'MYRA';
    private const SIG_REQUEST_PAYLOAD = 'myra-api-request';
    private const ALGO_KEY = 'sha256';
    private const ALGO_SIG = 'sha512';

    /**
     * @param string $secret
     * @param string $apiKey
     */
    public function __construct(
        private readonly string $secret,
        private readonly string $apiKey
    ) {
        $this->date = trim(date(DATE_RFC3339));
    }

    /**
     * @param RequestInterface $request
     * @return RequestInterface
     */
    public function signRequest(RequestInterface $request): RequestInterface
    {
        $signature = $this->generateSignature($request);
        $request = $request->withHeader('Content-Type', self::CONTENT_TYPE);
        $request = $request->withHeader('Date', $this->date);
        return $request->withHeader('Authorization', sprintf(self::SIG_KEY.' %s:%s', trim($this->apiKey), $signature));
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    private function generateSignature(RequestInterface $request): string
    {
        $dateKey = hash_hmac(self::ALGO_KEY, $this->date, self::SIG_KEY . trim($this->secret));
        $signingKey = hash_hmac(self::ALGO_KEY, self::SIG_REQUEST_PAYLOAD, $dateKey);

        $signingString = $this->generateSigningSignature($request);
        $signature = hash_hmac(self::ALGO_SIG, $signingString, $signingKey, true);
        return base64_encode($signature);
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    private function generateSigningSignature(RequestInterface $request): string
    {
        $bodyHash = $this->getRequestBodyHash($request);
        $requestMethod = $this->getRequestMethod($request);
        $path = $this->getRequestPath($request);

        return sprintf('%s#%s#%s#%s#%s', $bodyHash, $requestMethod, $path, self::CONTENT_TYPE, $this->date);
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    private function getRequestBodyHash(RequestInterface $request): string
    {
        $body = trim($request->getBody()->getContents());
        if (empty($body))
            return 'd41d8cd98f00b204e9800998ecf8427e'; // empty md5 hash

        return md5($body);
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    private function getRequestMethod(RequestInterface $request): string
    {
        return trim(strtoupper($request->getMethod()));
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    private function getRequestPath(RequestInterface $request): string
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        if (!empty($uri->getQuery())) {
            $path .= '?' . $uri->getQuery();
        }

        return trim($path);
    }
}
