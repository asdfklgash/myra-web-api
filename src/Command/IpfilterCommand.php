<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Command;


use DateTimeImmutable;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Myracloud\WebApi\Endpoint\Enum\IPFilterEnum;
use Myracloud\WebApi\Endpoint\IpFilter;
use RuntimeException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class IpfilterCommand
 *
 * @package Myracloud\WebApi\Command
 */
class IpfilterCommand extends AbstractCrudCommand
{
    protected function configure(): void
    {
        $this->setName('myracloud:api:ipfilter');
        $this->addOption('value', null, InputOption::VALUE_REQUIRED, 'Filter pattern', null);
        $this->addOption('type', 't', InputOption::VALUE_REQUIRED, 'Matching type', IPFilterEnum::Blacklist->value);

        $this->setDescription('Domain commands allow you to edit Ip based filters.');

        $this->setHelp(<<<'TAG'
Only passing fqdn without additional options will list all IpFilter entries.

<fg=yellow>Example Listing all IpFilter entries:</>
bin/console myracloud:api:ipfilter <fqdn>

<fg=yellow>Example creating a new IpFilter entry:</>
bin/console myracloud:api:ipfilter <fqdn> -o create --value <filter-pattern> --type <match-type>

<fg=yellow>Example updating a existing IpFilter entry:</>
bin/console myracloud:api:ipfilter <fqdn> -o update --id <id-from-list> --value <filter-pattern> --type <match-type>

<match-type> can be WHITELIST of BLACKLIST.

<fg=yellow>Example deleting a existing IpFilter entry:</>
bin/console myracloud:api:ipfilter -o delete --id <id-from-list>

TAG
        );

        parent::configure();
    }

    /**
     * @param array           $options
     * @param OutputInterface $output
     * @throws GuzzleException
     */
    protected function OpCreate(array $options, OutputInterface $output): void
    {
        if (empty($options['value'])) {
            throw new RuntimeException('You need to define a filter pattern via --value');
        }
        $type = IPFilterEnum::tryFrom($options['type']??'');
        if (!$type) {
            throw new RuntimeException('--type has to be one of ' . implode(',', IPFilterEnum::values()));
        }

        $endpoint = $this->getEndpoint();
        $return = $endpoint->create(
            $options['fqdn'],
            $type->realType(),
            $options['value']
        );
        $this->handleTableReturn($return, $output);
    }

    protected function getEndpoint(): IpFilter
    {
        return $this->webapi->getIpFilterEndpoint();
    }

    /**
     * @param                 $data
     * @param OutputInterface $output
     */
    protected function writeTable($data, OutputInterface $output): void
    {
        $table = new Table($output);
        $table->setHeaders([
            'Id',
            'Created',
            'Modified',
            'Value',
            'Type',
            'Enabled',
        ]);

        foreach ($data as $item) {
            $table->addRow([
                $item['id']??null,
                $item['created'],
                $item['modified'],
                $item['value'],
                IPFilterEnum::tryFrom($item['type'])?->value,
                ($item['enabled'] ?? false) ? 'true' : 'false',
            ]);
        }
        $table->render();
    }

    /**
     * @param array           $options
     * @param OutputInterface $output
     * @throws Exception|GuzzleException
     */
    protected function OpUpdate(array $options, OutputInterface $output): void
    {
        $endpoint = $this->getEndpoint();
        $existing = $this->findById($options);

        if (empty($options['value'])) {
            $options['value'] = $existing['value'];
        }

        $type = IPFilterEnum::tryFrom($options['type']??'');
        if (!$type) {
            throw new RuntimeException('--type has to be one of ' . implode(',', IPFilterEnum::values()));
        }

        $return = $endpoint->update(
            $options['fqdn'],
            $options['id'],
            new DateTimeImmutable($existing['modified']),
            $type,
            $options['value']
        );
        $this->handleTableReturn($return, $output);
    }
}
