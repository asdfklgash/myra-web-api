<?php
declare(strict_types=1);

namespace Myracloud\Tests\Endpoint;

use DateInterval;
use DateTime;
use GuzzleHttp\Exception\GuzzleException;
use Myracloud\WebApi\Endpoint\Statistic;

/**
 * Class StatisticTest
 *
 * @package Myracloud\WebApi\Endpoint
 */
class StatisticTest extends AbstractEndpointTest
{
    protected Statistic $statisticEndpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->statisticEndpoint = $this->api->getStatisticEndpoint();
        $this->assertThat($this->statisticEndpoint, $this->isInstanceOf('Myracloud\WebApi\Endpoint\Statistic'));
    }

    /**
     * @throws GuzzleException
     */
    public function testQuery(): void
    {
        $endDate = new DateTime();
        $startDate = clone $endDate;
        $startDate->sub(new DateInterval('P1D'));

        $query = [
            "query" => [
                "aggregationInterval" => 'hour',
                "dataSources" => [
                    'myr' => [
                        'source' => 'bytes_cache_hits',
                        'type' => 'stats',
                    ],
                ]
                ,
                'startDate' => $startDate->format('c'),
                'endDate' => $endDate->format('c'),
                'fqdn' => [
                    "ALL:" . self::TESTDOMAIN,
                ],
                'type' => 'fqdn',
            ],
        ];
        $result = $this->statisticEndpoint->query($query);
        var_dump($result);
        $this->assertArrayHasKey('objectType', $result);

        $this->assertEquals('StatisticVO', $result['objectType']);

        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('myr', $result['result']);
    }
}
