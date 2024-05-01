<?php
declare(strict_types=1);

namespace Myracloud\Tests\Endpoint;

use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Myracloud\WebApi\Endpoint\AbstractEndpoint;
use Myracloud\WebApi\Endpoint\DnsRecord;

/**
 * Class DnsRecordTest
 *
 * @package Myracloud\WebApi\Endpoint
 */
class DnsRecordTest extends AbstractEndpointTest
{
    protected DnsRecord $dnsRecordEndpoint;

    protected array $testData = [
        'create' => [
            'value' => '123.123.123.123',
            'priority' => 0,
            'ttl' => 333,
            'recordType' => AbstractEndpoint::DNS_TYPE_A,
            'active' => true,
            'enabled' => true,
            'paused' => false,
            'caaFlags' => 0,
        ],
        'update' => [
            'value' => '12.23.34.45',
            'priority' => 0,
            'ttl' => 333,
            'recordType' => AbstractEndpoint::DNS_TYPE_A,
            'active' => false,
            'enabled' => true,
            'paused' => false,
            'caaFlags' => 0,
        ],
        'list1' => [
            'value' => '22.222.222.222',
            'name' => 'someOtherName',
            'priority' => 0,
            'ttl' => 112233,
            'recordType' => AbstractEndpoint::DNS_TYPE_A,
            'active' => false,
            'enabled' => true,
            'paused' => false,
            'caaFlags' => 0,
        ],
        'list2' => [
            'value' => 'test test test',
            'name' => 'testname',
            'priority' => 0,
            'ttl' => 9999,
            'recordType' => AbstractEndpoint::DNS_TYPE_TXT,
            'active' => false,
            'enabled' => true,
            'paused' => false,
            'caaFlags' => 0,
        ],
    ];

    protected string $subDomain = 'subdomain';
    protected string $subDomain2 = 'otherdomain';

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->dnsRecordEndpoint = $this->api->getDnsRecordEndpoint();
        $this->assertThat($this->dnsRecordEndpoint, $this->isInstanceOf('Myracloud\WebApi\Endpoint\DnsRecord'));

        $this->testData['create']['name'] = $this->subDomain . '.' . self::TESTDOMAIN;
        $this->testData['create']['alternativeCname'] = $this->subDomain . '-' . str_replace('.', '-', self::TESTDOMAIN) . '.ax4z.com.';

        $this->testData['update']['name'] = $this->subDomain2 . '.' . self::TESTDOMAIN;
        $this->testData['update']['alternativeCname'] = $this->subDomain2 . '-' . str_replace('.', '-', self::TESTDOMAIN) . '.ax4z.com.';
    }

    /**
     * @throws GuzzleException
     */
    public function testUpdate(): void
    {
        $this->testCreate();
        $list = $this->dnsRecordEndpoint->getList(self::TESTDOMAIN);
        foreach ($list['list'] as $item) {
            if ($item['name'] == $this->testData['create']['name']) {
                $result = $this->dnsRecordEndpoint->update(
                    self::TESTDOMAIN,
                    $item['id'],
                    new DateTime($item['modified']),
                    $this->subDomain2,
                    $this->testData['update']['value'],
                    $this->testData['update']['ttl'],
                    $this->testData['update']['recordType'],
                    $this->testData['update']['active']
                );
                $this->verifyNoError($result);
                $this->verifyTargetObject($result, 'DnsRecordVO');
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
        $result = $this->dnsRecordEndpoint->create(
            self::TESTDOMAIN,
            $this->subDomain,
            $this->testData['create']['value'],
            $this->testData['create']['ttl'],
            $this->testData['create']['recordType']
        );

        $this->verifyNoError($result);
        $this->verifyTargetObject($result, 'DnsRecordVO');
        $this->verifyFields($result['targetObject'][0], $this->testData['create']);
    }

    /**
     * @throws GuzzleException
     */
    public function testDelete(): void
    {
        $list = $this->dnsRecordEndpoint->getList(self::TESTDOMAIN);
        foreach ($list['list'] as $item) {
            if (
                $item['name'] == $this->testData['create']['name']
                || $item['name'] == $this->testData['update']['name']
            ) {
                $result = $this->dnsRecordEndpoint->delete(
                    self::TESTDOMAIN,
                    $item['id'],
                    new DateTime($item['modified'])
                );
                $this->verifyNoError($result);
            }
        }
    }

    /**
     *
     */
    public function testGetList(): void
    {
        $this->testCreate();
        $result = $this->dnsRecordEndpoint->getList(self::TESTDOMAIN);
        $this->verifyListResult($result);
    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function testGetListFiltered(): void
    {
        $this->dnsRecordEndpoint->create(
            self::TESTDOMAIN,
            $this->subDomain,
            $this->testData['create']['value'],
            $this->testData['create']['ttl'],
            $this->testData['create']['recordType'],
            $this->testData['create']['active']
        );
        $this->dnsRecordEndpoint->create(
            self::TESTDOMAIN,
            $this->testData['list1']['name'],
            $this->testData['list1']['value'],
            $this->testData['list1']['ttl'],
            $this->testData['list1']['recordType'],
            $this->testData['list1']['active']
        );
        $this->dnsRecordEndpoint->create(
            self::TESTDOMAIN,
            $this->testData['list2']['name'],
            $this->testData['list2']['value'],
            $this->testData['list2']['ttl'],
            $this->testData['list2']['recordType'],
            $this->testData['list2']['active']

        );
        /**
         * List only A Records
         */
        $result = $this->dnsRecordEndpoint->getList(self::TESTDOMAIN, 1, null, AbstractEndpoint::DNS_TYPE_A);
        $this->verifyNoError($result);

        $this->assertEquals(2, count($result['list']));
        foreach ($result['list'] as $item) {
            $this->assertEquals(AbstractEndpoint::DNS_TYPE_A, $item['recordType']);
        }

        /**
         * List only active
         */
        $result = $this->dnsRecordEndpoint->getList(self::TESTDOMAIN, 1, null, null, true);

        $this->verifyNoError($result);

        $this->assertEquals(1, count($result['list']));
        foreach ($result['list'] as $item) {
            $this->assertEquals(true, $item['active']);
        }

        /**
         * List only with substring in name
         */
        $result = $this->dnsRecordEndpoint->getList(self::TESTDOMAIN, 1, 'sub');
        $this->verifyNoError($result);

        $this->assertEquals(1, count($result['list']));
        foreach ($result['list'] as $item) {
            $this->assertContains('sub', $item['name']);
        }
    }
}
