<?php
declare(strict_types=1);

namespace Myracloud\Tests\Endpoint;

use DateTime;
use GuzzleHttp\Exception\GuzzleException;
use Myracloud\WebApi\Endpoint\AbstractEndpoint;
use Myracloud\WebApi\Endpoint\Redirect;

/**
 * Class RedirectTest
 *
 * @package Myracloud\Tests\Endpoint
 */
class RedirectTest extends AbstractEndpointTest
{
    protected Redirect $redirectEndpoint;

    protected array $testData = [
        'create' => [
            'source' => '/test_source',
            'destination' => '/test_dest',
            'type' => AbstractEndpoint::REDIRECT_TYPE_REDIRECT,
            'subDomainName' => self::TESTDOMAIN . '.',
            'matchingType' => AbstractEndpoint::MATCHING_TYPE_PREFIX,
        ],
        'update' => [
            'source' => '/test_source_changed',
            'destination' => '/test_destination_changed',
            'type' => AbstractEndpoint::REDIRECT_TYPE_REDIRECT,
            'subDomainName' => self::TESTDOMAIN . '.',
            'matchingType' => AbstractEndpoint::MATCHING_TYPE_PREFIX,
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->redirectEndpoint = $this->api->getRedirectEndpoint();
        $this->assertThat($this->redirectEndpoint, $this->isInstanceOf('Myracloud\WebApi\Endpoint\Redirect'));
    }

    /**
     * @throws GuzzleException
     */
    public function testUpdate(): void
    {
        $this->testCreate();
        $list = $this->redirectEndpoint->getList(self::TESTDOMAIN);
        foreach ($list['list'] as $item) {
            if ($item['source'] == $this->testData['create']['source']) {
                $result = $this->redirectEndpoint->update(
                    self::TESTDOMAIN,
                    $item['id'],
                    new DateTime($item['modified']),
                    $this->testData['update']['source'],
                    $this->testData['update']['destination']
                );

                $this->verifyNoError($result);
                $this->verifyTargetObject($result, 'DnsRedirectVO');
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
        $result = $this->redirectEndpoint->create(
            self::TESTDOMAIN,
            $this->testData['create']['source'],
            $this->testData['create']['destination']
        );
        $this->verifyNoError($result);
        $this->verifyTargetObject($result, 'DnsRedirectVO');
        $this->verifyFields($result['targetObject'][0], $this->testData['create']);
    }

    /**
     * @throws GuzzleException
     */
    public function testDelete(): void
    {
        $list = $this->redirectEndpoint->getList(self::TESTDOMAIN);
        foreach ($list['list'] as $item) {
            if (
                $item['source'] == $this->testData['create']['source']
                || $item['source'] == $this->testData['update']['source']
            ) {
                $result = $this->redirectEndpoint->delete(
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
        $result = $this->redirectEndpoint->getList(self::TESTDOMAIN);
        $this->verifyListResult($result);
        var_dump($result);
    }
}

