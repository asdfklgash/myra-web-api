<?php
declare(strict_types=1);

namespace Myracloud\Tests\Endpoint;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Myracloud\WebApi\WebApi;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractEndpointTest
 *
 * @package Myracloud\Tests\Endpoint
 */
abstract class AbstractEndpointTest extends TestCase
{
    public const TESTDOMAIN = 'myratest.org';

    protected WebApi $api;

    protected function setUp(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], 'First response'),
            new RequestException("Error Communicating with Server", new Request('GET', 'test')),
            new Response(200, [], 'Third response')
        ]);

        $config = require __DIR__ . '/../Config.php';
        $this->api = new WebApi(
            $config['apiKey'],
            $config['secret'],
            'beta.myracloud.com',
            requestHandler: $mockHandler
        );
        $this->assertThat($this->api, $this->isInstanceOf('Myracloud\WebApi\WebApi'));
    }

    /**
     * @param $result
     */
    protected function verifyListResult($result): void
    {
        $this->verifyNoError($result);

        $this->assertArrayHasKey('page', $result);
        $this->assertEquals(1, $result['page']);


        $this->assertArrayHasKey('list', $result);
        $this->assertIsArray($result['list']);


        $this->assertArrayHasKey('count', $result);
        $this->assertEquals(count($result['list']), $result['count']);
    }

    /**
     * @param $result
     */
    protected function verifyNoError($result): void
    {
        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals(false, $result['error'], 'Result contained Error Flag.' . var_export($result, true));
    }

    /**
     * @param $result
     * @param $data
     */
    protected function verifyFields($result, $data): void
    {
        foreach ($data as $key => $value) {
            $this->assertArrayHasKey($key, $result, 'Expected Key ' . $key . ' was not found.');
            $this->assertEquals($value, $result[$key], $key . ' was ' . $result[$key] . ' and not expected ' . $value);
        }
    }

    /**
     * @param $result
     * @param $type
     */
    protected function verifyTargetObject($result, $type): void
    {
        $this->assertArrayHasKey('targetObject', $result);
        $this->assertGreaterThan(0, count($result['targetObject']));
        $this->assertIsArray($result['targetObject'][0]);

        $this->assertArrayHasKey('objectType', $result['targetObject'][0]);
        $this->assertEquals($type, $result['targetObject'][0]['objectType']);
    }

}
