<?php
declare(strict_types=1);

namespace Myracloud\Tests\Endpoint;

use Myracloud\WebApi\Endpoint\Networks;

/**
 * Class NetworksTest
 *
 * @package Myracloud\Tests\Endpoint
 */
class NetworksTest extends AbstractEndpointTest
{
    protected Networks $networksEndpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->networksEndpoint = $this->api->getNetworksEndpoint();
        $this->assertThat($this->networksEndpoint, $this->isInstanceOf('Myracloud\WebApi\Endpoint\Networks'));
    }

    /**
     *
     */
    public function testGetList(): void
    {
        $list = $this->networksEndpoint->getList(self::TESTDOMAIN);
        $this->verifyNoError($list);

        $this->verifyListResult($list);
        foreach ($list['list'] as $item) {
            $this->verifyFields($item, [
                'objectType' => 'IpRangeVO',
            ]);
        }
    }
}
