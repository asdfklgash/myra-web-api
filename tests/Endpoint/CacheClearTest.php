<?php
declare(strict_types=1);

namespace Myracloud\Tests\Endpoint;

use GuzzleHttp\Exception\GuzzleException;
use Myracloud\WebApi\Endpoint\CacheClear;

class CacheClearTest extends AbstractEndpointTest
{
    protected CacheClear $cacheClearEndpoint;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheClearEndpoint = $this->api->getCacheClearEndpoint();
        $this->assertThat($this->cacheClearEndpoint, $this->isInstanceOf('Myracloud\WebApi\Endpoint\CacheClear'));
    }

    /**
     * @throws GuzzleException
     */
    public function testClear(): void
    {
        $result = $this->cacheClearEndpoint->clear(self::TESTDOMAIN, self::TESTDOMAIN, '*');
        var_dump($result);
    }
}
