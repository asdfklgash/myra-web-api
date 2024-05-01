<?php
declare(strict_types=1);

namespace Myracloud\Tests\Endpoint;

use DateTime;
use GuzzleHttp\Exception\GuzzleException;
use Myracloud\WebApi\Endpoint\Domain;

/**
 * Class DomainTest
 *
 * @package Myracloud\Tests\Endpoint
 */
class DomainTest extends AbstractEndpointTest
{
    protected Domain $domainEndpoint;

    protected array $testData = [
        'create' => [
            'name' => self::TESTDOMAIN,
            'maintenance' => false,
            'paused' => false,
            'autoUpdate' => false,
            'owned' => true,
            'reversed' => false,
            'environment' => 'live',
        ],
        'update' => [
            'name' => self::TESTDOMAIN,
            'autoUpdate' => true,
        ],
    ];

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->domainEndpoint = $this->api->getDomainEndpoint();
        $this->assertThat($this->domainEndpoint, $this->isInstanceOf('Myracloud\WebApi\Endpoint\Domain'));
    }

    /**
     * @throws GuzzleException
     */
    public function testUpdate(): void
    {
        $this->testCreate();
        $list = $this->domainEndpoint->getList(self::TESTDOMAIN);
        foreach ($list['list'] as $item) {
            if ($item['name'] == self::TESTDOMAIN) {
                $result = $this->domainEndpoint->update(
                    $item['id'],
                    new DateTime($item['modified']),
                    $this->testData['update']['autoUpdate']
                );
                $this->verifyNoError($result);

                $this->verifyTargetObject($result, 'DomainVO');
                $this->verifyFields($result['targetObject'][0], $this->testData['update']);
            }
        }
    }

    /**
     * @throws GuzzleException
     */
    public function testCreate(): void
    {
        $this->testDelete();
        $result = $this->domainEndpoint->create(self::TESTDOMAIN);

        $this->verifyNoError($result);

        $this->verifyTargetObject($result, 'DomainVO');

        $this->verifyFields($result['targetObject'][0], $this->testData['create']);
    }

    /**
     * @throws GuzzleException
     */
    public function testDelete(): void
    {
        $list = $this->domainEndpoint->getList(self::TESTDOMAIN);
        foreach ($list['list'] as $item) {
            if ($item['name'] == self::TESTDOMAIN) {
                $result = $this->domainEndpoint->delete(
                    $item['name'],
                    $item['id'],
                    new DateTime($item['modified'])
                );
                $this->verifyNoError($result);
            }
        }
        $list = $this->domainEndpoint->getList(self::TESTDOMAIN);
    }

    /**
     *
     */
    public function testGetList(): void
    {
        $this->testCreate();
        $result = $this->domainEndpoint->getList(self::TESTDOMAIN);
        $this->verifyListResult($result);
    }
}
