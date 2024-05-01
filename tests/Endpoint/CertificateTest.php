<?php
declare(strict_types=1);

namespace Myracloud\Tests\Endpoint;

use Myracloud\WebApi\Endpoint\Certificate;

/**
 * Class CertificateTest
 *
 * @package Myracloud\Tests\Endpoint
 */
class CertificateTest extends AbstractEndpointTest
{
    protected Certificate $certificateEndpoint;

    protected array $testData = [
        'create' => [
        ],
    ];

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->certificateEndpoint = $this->api->getCertificateEndpoint();
        $this->assertThat($this->certificateEndpoint, $this->isInstanceOf('Myracloud\WebApi\Endpoint\Certificate'));
    }

    /**
     *
     */
    public function testGetList(): void
    {
        $list = $this->certificateEndpoint->getList(self::TESTDOMAIN);
        var_dump($list);
    }

    public function testCreate(): void
    {
        $list = $this->certificateEndpoint->create(self::TESTDOMAIN);
        var_dump($list);
    }
}
