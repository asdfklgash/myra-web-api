<?php
declare(strict_types=1);


namespace Myracloud\WebApi\Command;

use DateTimeImmutable;
use GuzzleHttp\Exception\GuzzleException;
use Myracloud\WebApi\Endpoint\DnsRecord;
use Myracloud\WebApi\Endpoint\Enum\DNSEnum;
use RuntimeException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DnsCommand
 *
 * @package Myracloud\API\Command
 */
class DnsCommand extends AbstractCrudCommand
{
    /**
     *
     */
    protected function configure(): void
    {
        $this->setName('myracloud:api:dns');
        $this->addOption('ttl', null, InputOption::VALUE_REQUIRED, 'time to live', null);
        $this->addOption('sub', null, InputOption::VALUE_REQUIRED, 'subdomain', null);
        $this->addOption('ip', null, InputOption::VALUE_REQUIRED, 'IpAddress', null);
        $this->addOption('sslcert', null, InputOption::VALUE_REQUIRED, 'Path to a SslCert', null);
        $this->addOption('type', null, InputOption::VALUE_REQUIRED, 'Type of match (' . implode(',', DNSEnum::values()) . ')', null);

        $this->setDescription('Dns commands allow you to edit DNS Records.');
        $this->setHelp(sprintf(<<<'TAG'
Only passing fqdn without additional options will list all Dns entries.

<fg=yellow>Example Listing all Dns entries:</>
bin/console myracloud:api:dns <fqdn>

<fg=yellow>Example creating a new dns entry:</>
bin/console myracloud:api:dns <fqdn> -o create --sub <name> --ttl <ttl> --type <type> --ip <ipaddress/value>

Please note, additional rules for the format of the ipaddress/value apply depending on the entry type.

<fg=yellow>Example updating a existing Dns entry:</>
bin/console myracloud:api:dns <fqdn> -o update --id <id-from-list> <any-param>

Update an existing record, use the 'update' operation with an existing id. You can add any of the create params (ttl,type,ip,sub) so overwrite the existing value.

<fg=yellow>Example Deleting a existing Dns entry:</>
bin/console myracloud:api:dns <fqdn> -o delete --id <id-from-list>

valid types are %s

TAG
            , implode(',', DNSEnum::values())));
        parent::configure();
    }

    /**
     * @param array           $options
     * @param OutputInterface $output
     * @return void
     * @throws GuzzleException
     */
    protected function OpCreate(array $options, OutputInterface $output): void
    {

        if (empty($options['ttl'])) {
            throw new RuntimeException('You need to define a time to live via --ttl');
        }
        $type = DNSEnum::tryFrom($options['type']??'');
        if (!$type) {
            throw new RuntimeException('--type has to be one of ' . implode(',', DNSEnum::values()));
        }
        if (empty($options['sub'])) {
            throw new RuntimeException('You need to define a subdomain via --sub');
        }
        if (empty($options['ip'])) {
            throw new RuntimeException('You need to define a IpAddress via --ip');
        }
        if ($options['sslcert'] !== null && !is_readable(realpath($options['sslcert']))) {
            throw new RuntimeException(sprintf('Could not find given file "%s".', $options['sslcert']));
        }

        $return = $this->getEndpoint()->create(
            $options['fqdn'],
            $options['sub'],
            $options['ip'],
            $options['ttl'],
            $type,
            true,
            $options['sslcert'] ? file_get_contents(realpath($options['sslcert'])) : ''
        );
        $this->handleTableReturn($return, $output);
    }

    protected function getEndpoint(): DnsRecord
    {
        return $this->webapi->getDnsRecordEndpoint();
    }

    /**
     * @param array           $options
     * @param OutputInterface $output
     * @return void
     * @throws GuzzleException
     */
    protected function OpUpdate(array $options, OutputInterface $output): void
    {
        $endpoint = $this->getEndpoint();
        $existing = $this->findById($options);

        if (empty($options['ttl'])) {
            $options['ttl'] = $existing['ttl'];
        }
        $type = DNSEnum::tryFrom($options['type']??$existing['recordType']??'');
        if (!$type) {
            throw new RuntimeException('--type has to be one of ' . implode(',', DNSEnum::values()));
        }
        if (empty($options['sub'])) {
            $options['sub'] = $existing['name'];
        }
        if (empty($options['ip'])) {
            $options['ip'] = $existing['value'];
        }
        if ($options['sslcert'] !== null && !is_readable(realpath($options['sslcert']))) {
            throw new RuntimeException(sprintf('Could not find given file "%s".', $options['sslcert']));
        }


        $return = $endpoint->update(
            $options['fqdn'],
            $options['id'],
            new DateTimeImmutable($existing['modified']),
            $options['sub'],
            $options['ip'],
            $options['ttl'],
            $type,
            $existing['active'],
            $options['sslcert'] ? file_get_contents(realpath($options['sslcert'])) : null
        );
        $this->handleTableReturn($return, $output);
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
            'Name',
            'Value',
            'Priority',
            'ttl',
            'Type',
            'Enabled',
            'Paused',
            'Alternative N.',
            'caaFlags',
        ]);

        foreach ($data as $item) {
            $table->addRow([
                $item['id'] ?? null,
                $item['created'],
                $item['modified'],
                $item['name'],
                $item['value'],
                $item['priority'],
                $item['ttl'],
                DNSEnum::tryFrom($item['recordType']??'')?->value,
                ($item['enabled'] ?? false) ? 'true' : 'false',
                ($item['paused'] ?? false) ? 'true' : 'false',
                $item['alternativeCname']??'',
                $item['caaFlags'],
            ]);
        }
        $table->render();
    }
}
