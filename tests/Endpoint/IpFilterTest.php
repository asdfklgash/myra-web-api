<?php
declare(strict_types=1);

namespace Myracloud\Tests\Endpoint;

use DateTime;
use GuzzleHttp\Exception\GuzzleException;
use Myracloud\WebApi\Endpoint\AbstractEndpoint;
use Myracloud\WebApi\Endpoint\IpFilter;

/**
 * Class IpFilterTest
 *
 * @package Myracloud\Tests\Endpoint
 */
class IpFilterTest extends AbstractEndpointTest
{
    /** @var IpFilter */
    protected IpFilter $ipFilterEndpoint;

    protected array $testData = [
        'create' => [
            'type' => AbstractEndpoint::IPFILTER_TYPE_BLACKLIST,
            'value' => '1.2.3.4/32',
        ],
        'update' => [
            'type' => AbstractEndpoint::IPFILTER_TYPE_WHITELIST,
            'value' => '5.6.7.8/32',
        ],
    ];

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->ipFilterEndpoint = $this->api->getIpFilterEndpoint();
        $this->assertThat($this->ipFilterEndpoint, $this->isInstanceOf('Myracloud\WebApi\Endpoint\IpFilter'));
    }

    /**
     * @throws GuzzleException
     */
    public function testGetList(): void
    {
        $this->testCreate();
        $result = $this->ipFilterEndpoint->getList(self::TESTDOMAIN);
        $this->verifyListResult($result);
        var_export($result);
    }

    /**
     * @throws GuzzleException
     */
    public function testCreate(): void
    {
        $this->testDelete();
        $result = $this->ipFilterEndpoint->create(
            self::TESTDOMAIN,
            $this->testData['create']['type'],
            $this->testData['create']['value']

        );

        $this->verifyNoError($result);
        $this->verifyTargetObject($result, 'IpFilterVO');
        $this->verifyFields($result['targetObject'][0], $this->testData['create']);
    }

    /**
     * @throws GuzzleException
     */
    public function testDelete(): void
    {
        $list = $this->ipFilterEndpoint->getList(self::TESTDOMAIN);

        foreach ($list['list'] as $item) {
            if (
                $item['value'] == $this->testData['create']['value']
                || $item['value'] == $this->testData['update']['value']
            ) {
                $result = $this->ipFilterEndpoint->delete(
                    self::TESTDOMAIN,
                    $item['id'],
                    new DateTime($item['modified'])
                );
                $this->verifyNoError($result);
                $this->verifyTargetObject($result, 'IpFilterVO');
            }
        }
    }

    public function testUpdate(): void
    {
        $list = $this->ipFilterEndpoint->getList(self::TESTDOMAIN);

        foreach ($list['list'] as $item) {
            if ($item['value'] == $this->testData['create']['value']) {
                $result = $this->ipFilterEndpoint->update(
                    self::TESTDOMAIN,
                    $item['id'],
                    new DateTime($item['modified']),
                    $this->testData['update']['type'],
                    $this->testData['update']['value']
                );
                var_dump($result);
                $this->verifyNoError($result);
                $this->verifyTargetObject($result, 'IpFilterVO');
                $this->verifyFields($result['targetObject'][0], $this->testData['update']);

            }
        }
    }
}
