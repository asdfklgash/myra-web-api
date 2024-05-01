<?php
declare(strict_types=1);

namespace Myracloud\Tests\Endpoint;

use DateInterval;
use DateTime;
use GuzzleHttp\Exception\GuzzleException;
use Myracloud\WebApi\Endpoint\Maintenance;

/**
 * Class MaintenanceTest
 *
 * @package Myracloud\Tests\Endpoint
 */
class MaintenanceTest extends AbstractEndpointTest
{
    /** @var Maintenance */
    protected Maintenance $maintenanceEndpoint;

    protected array $testData = [
        'create' => [
            'fqdn' => self::TESTDOMAIN,
            'content' => 'Maintenance Page',
            'active' => true,
        ],
        'update' => [
            'fqdn' => self::TESTDOMAIN,
            'content' => 'Maintenande Page changed',
        ],
        'default' => [
            'label' => 'aaaaaaaaaaaaaaaa',
            'value' => 'bbbbbbbbbbbbbbbb',
            'twitter' => 'cccccccccccccccc',
            'facebook' => 'dddddddddddddddd',
        ],
    ];


    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->maintenanceEndpoint = $this->api->getMaintenanceEndpoint();
        $this->assertThat($this->maintenanceEndpoint, $this->isInstanceOf('Myracloud\WebApi\Endpoint\Maintenance'));
    }

    /**
     * @throws GuzzleException
     */
    public function testUpdate(): void
    {
        $this->testCreate();
        $list = $this->maintenanceEndpoint->getList(self::TESTDOMAIN);

        foreach ($list['list'] as $item) {
            if ($item['content'] == $this->testData['create']['content']) {
                $start = new DateTime($item['start']);
                $start->add(new DateInterval('P7D'));
                $end = new DateTime($item['end']);
                $end->add(new DateInterval('P11D'));

                $result = $this->maintenanceEndpoint->update(
                    self::TESTDOMAIN,
                    $item['id'],
                    new DateTime(),
                    $start,
                    $end,
                    $this->testData['update']['content']
                );
                var_export($result);
                $this->verifyNoError($result);
                $this->verifyTargetObject($result, 'MaintenanceVO');
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
        $endDate = new DateTime();
        $startDate = clone $endDate;
        $startDate->sub(new DateInterval('P1D'));

        $result = $this->maintenanceEndpoint->create(
            self::TESTDOMAIN,
            $startDate,
            $endDate,
            $this->testData['create']['content']
        );

        $this->verifyNoError($result);
        $this->verifyTargetObject($result, 'MaintenanceVO');
        $this->verifyFields($result['targetObject'][0], $this->testData['create']);
    }

    /**
     * @throws GuzzleException
     */
    public function testDelete(): void
    {
        $list = $this->maintenanceEndpoint->getList(self::TESTDOMAIN);

        foreach ($list['list'] as $item) {
            $res = $this->maintenanceEndpoint->delete(
                self::TESTDOMAIN,
                $item['id'],
                new DateTime($item['modified'])
            );
        }
    }

    /**
     * @throws GuzzleException
     */
    public function testGetList(): void
    {
        $this->testCreate();
        $result = $this->maintenanceEndpoint->getList(self::TESTDOMAIN);
        $this->verifyListResult($result);
    }

    /**
     * @throws GuzzleException
     */
    public function testCreateDefault(): void
    {
        $this->testDelete();
        $endDate = new DateTime();
        $startDate = clone $endDate;
        $startDate->sub(new DateInterval('P1D'));

        $result = $this->maintenanceEndpoint->createDefaultPage(
            self::TESTDOMAIN,
            $startDate,
            $endDate,
            $this->testData['default']['label'],
            $this->testData['default']['value'],
            $this->testData['default']['facebook'],
            $this->testData['default']['twitter']
        );
        $this->verifyNoError($result);
        $this->verifyTargetObject($result, 'MaintenanceVO');


        $this->assertArrayHasKey('targetObject', $result);

        $this->assertEquals(1, count($result['targetObject']));

        $this->assertStringContainsString($this->testData['default']['label'], $result['targetObject'][0]['content']);
        $this->assertStringContainsString($this->testData['default']['value'], $result['targetObject'][0]['content']);
        $this->assertStringContainsString($this->testData['default']['facebook'],
            $result['targetObject'][0]['content']);
        $this->assertStringContainsString($this->testData['default']['twitter'], $result['targetObject'][0]['content']);
    }
}
