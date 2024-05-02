<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Command;

use DateTimeImmutable;
use GuzzleHttp\Exception\GuzzleException;
use Myracloud\WebApi\Command\Enum\ExecutionTypeEnum;
use Myracloud\WebApi\Endpoint\Domain;
use RuntimeException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DomainCommand
 *
 * @package Myracloud\WebApi\Command
 */
class DomainCommand extends AbstractCrudCommand
{
    protected bool $fqdnOptional = true;

    protected function configure(): void
    {
        parent::configure();
        $this->setName('myracloud:api:domain');
        $this->addOption('operation', 'o', InputOption::VALUE_REQUIRED, '', ExecutionTypeEnum::List->value);
        $this->addOption('id', null, InputOption::VALUE_REQUIRED, 'Id to Update/Delete');

        $this->addOption('autoupdate', null, InputOption::VALUE_REQUIRED, 'Auto update flag for the domain', null);
        $this->addOption('name', null, InputOption::VALUE_REQUIRED, 'Name of the domain', null);

        $this->setDescription('Domain commands allow you to edit Domain entries.');
        $this->setHelp(<<<'TAG'
Calling this command without additional options will list all Domain entries.

<fg=yellow>Example Listing all Domain entries:</>
bin/console myracloud:api:domain

<fg=yellow>Example creating a new Domain entry:</>
bin/console myracloud:api:domain -o create --name <fqdn>

<fg=yellow>Example deleting a existing Domain entry:</>
bin/console myracloud:api:domain -o delete --id <id-from-list>

TAG
        );
    }

    /**
     * @param array $options
     * @param OutputInterface $output
     * @return void
     * @throws GuzzleException
     */
    protected function OpList(array $options, OutputInterface $output): void
    {
        $options['fqdn'] = null;
        parent::OpList($options, $output);
    }

    /**
     * @param array           $options
     * @param OutputInterface $output
     * @return void
     * @throws GuzzleException
     */
    protected function OpCreate(array $options, OutputInterface $output): void
    {
        if ($options['name'] == null) {
            throw new RuntimeException('You need to define a domain name via --name');
        }
        $options['autoupdate'] ??= true;
        $return = $this->getEndpoint()->create(
            $options['name'],
            boolval($options['autoupdate'])

        );
        $this->handleTableReturn($return, $output);
    }

    /**
     * @return Domain
     */
    protected function getEndpoint(): Domain
    {
        return $this->webapi->getDomainEndpoint();
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
            'Autoupdate',
            'Maintenance',
            'Paused',
            'Owned',
            'Reversed',
            'Env.',
        ]);

        foreach ($data as $item) {
            $table->addRow([
                $item['id'] ?? null,
                $item['created'] ?? '',
                $item['modified'] ?? '',
                $item['name'] ?? '',
                ($item['autoUpdate']  ?? false) ? 'true' : 'false',
                ($item['maintenance'] ?? false) ? 'true' : 'false',
                ($item['paused'] ?? false) ? 'true' : 'false',
                ($item['owned'] ?? false) ? 'true' : 'false',
                ($item['reversed'] ?? false) ? 'true' : 'false',
                $item['environment']??'',
            ]);
        }
        $table->render();
    }

    /**
     * @param array           $options
     * @param OutputInterface $output
     * @return void
     * @throws GuzzleException
     */
    protected function OpUpdate(array $options, OutputInterface $output): void
    {
        $options['fqdn'] = null;
        $existing = $this->findById($options);
        $options['autoupdate'] ??= $existing['autoUpdate'] ?? false;
        $return = $this->getEndpoint()->update(
            $options['id'],
            new DateTimeImmutable($existing['modified']),
            (bool)($options['autoupdate'])
        );

        $this->handleTableReturn($return, $output);
    }

    /**
     * @param array           $options
     * @param OutputInterface $output
     * @throws GuzzleException
     */
    protected function OpDelete(array $options, OutputInterface $output): void
    {
        $options['fqdn'] = null;
        if ($options['id'] == null) {
            throw new RuntimeException('You need to define the id of the object to delete via --id');
        }
        $existing = $this->findById($options);
        $return   = $this->getEndpoint()->delete($existing['name'], $options['id'], new DateTimeImmutable($existing['modified']));
        $this->handleDeleteReturn($return, $output);
    }
}
