<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Command;

use DateTimeImmutable;
use GuzzleHttp\Exception\GuzzleException;
use Myracloud\WebApi\Endpoint\CacheSetting;
use Myracloud\WebApi\Endpoint\Enum\MatchEnum;
use RuntimeException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CacheSettingCommand
 *
 * @package Myracloud\API\Command
 */
class CacheSettingCommand extends AbstractCrudCommand
{
    /**
     *
     */
    protected function configure(): void
    {
        $this->setName('myracloud:api:cacheSetting');
        $this->addOption('path', 'p', InputOption::VALUE_REQUIRED, 'Path to match against', null);
        $this->addOption('ttl', null, InputOption::VALUE_REQUIRED, 'time to live', null);
        $this->addOption('type', null, InputOption::VALUE_REQUIRED, 'Type of match (' . implode(',', MatchEnum::values()) . ')', null);
        $this->setDescription('CacheSetting allows you to define/modify Cache rules.');
        $this->setHelp(<<<'TAG'
Only passing fqdn without additional options will display all known settings.

<fg=yellow>Example Listing all Cache Settings:</>
bin/console myracloud:api:cacheSetting <fqdn>

<fg=yellow>Example creating a setting for a Caching TTL of 1200 seconds for all resources under the page root:</>
bin/console myracloud:api:cacheSetting <fqdn> -o create --path / --ttl 1200 --type prefix

<fg=yellow>Example Deleting a existing Cache Setting:</>
bin/console myracloud:api:cacheSetting <fqdn> -o delete --id <id-from-list>
TAG
        );
        parent::configure();
    }


    /**
     * @param array $options
     * @param OutputInterface $output
     * @return void
     * @throws GuzzleException
     */
    protected function OpCreate(array $options, OutputInterface $output): void
    {
        if (empty($options['path'])) {
            throw new RuntimeException('You need to define a path to match via --path');
        }
        if (empty($options['ttl'])) {
            throw new RuntimeException('You need to define a time to live via --ttl');
        }
        $type = MatchEnum::tryFrom($options['type']??'');
        if (!$type) {
            throw new RuntimeException('--type has to be one of ' . implode(',', MatchEnum::values()));
        }
        $return   = $this->getEndpoint()->create($options['fqdn'], $options['path'], $options['ttl'], $type);
        $this->handleTableReturn($return, $output);
    }

    /**
     * @return CacheSetting
     */
    protected function getEndpoint(): CacheSetting
    {
        return $this->webapi->getCacheSettingsEndpoint();
    }

    /**
     * @param                 $data
     * @param OutputInterface $output
     */
    protected function writeTable($data, OutputInterface $output): void
    {
        $table = new Table($output);
        $table->setHeaders(['Id', 'Created', 'Modified', 'Path', 'ttl', 'not found ttl', 'Type', 'Enforce', 'Sort']);

        foreach ($data as $item) {
            $table->addRow([
                $item['id'] ?? null,
                $item['created'],
                $item['modified'],
                $item['path'],
                $item['ttl'],
                $item['notFoundTtl'],
                $item['type'],
                ($item['enforce'] ?? false) ? 'true' : 'false',
                $item['sort'],
            ]);
        }
        $table->render();
    }

    /**
     * @param array $options
     * @param OutputInterface $output
     * @return void
     * @throws GuzzleException
     */
    protected function OpUpdate(array $options, OutputInterface $output): void
    {
        $existing = $this->findById($options);
        if (empty($options['path'])) {
            $options['path'] = $existing['path'];
        }
        if (empty($options['ttl'])) {
            $options['ttl'] = $existing['ttl'];
        }

        $type = MatchEnum::tryFrom($options['type']??$existing['type']??'');
        if (!$type) {
            throw new RuntimeException('--type has to be one of ' . implode(',', MatchEnum::values()));
        }

        $return = $this->getEndpoint()->update($options['fqdn'], $existing['id'], new DateTimeImmutable($existing['modified']), $options['path'], $options['ttl'], $type);
        $this->handleTableReturn($return, $output);
    }
}
