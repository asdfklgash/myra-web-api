<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Command;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\TransferException;
use Myracloud\WebApi\Endpoint\AbstractEndpoint;
use Myracloud\WebApi\Endpoint\CacheClear;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CacheClearCommand
 *
 * @package Myracloud\API\Command
 */
class BindCommand extends AbstractCrudCommand
{
    /**
     *
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setName('myracloud:api:bind');
        $this->addOption('raw', 'r', InputOption::VALUE_NONE, 'Get the raw bind zone');
        $this->setDescription('Bind commands allows you to fetch the DNS zone as text or JSON data via Myra API.');
        $this->setHelp(<<<'TAG'
<fg=yellow>Example usage:</>
bin/console myracloud:api:bind <fqdn>

<fg=yellow>Example Get raw bind zone:</>
bin/console myracloud:api:bind <fqdn> -r
TAG
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {

            $options = $this->resolveOptions($input, $output);
            /** @var CacheClear $endpoint */
            if($options['raw'])
            {
                $endpoint = $this->webapi->getBindRawEndpoint();
            }
            else
            {
                $endpoint = $this->webapi->getBindEndpoint();
            }
            $return   = $endpoint->get($options['fqdn']);
        } catch (TransferException $e) {
            $this->handleTransferException($e, $output);

            return self::FAILURE;
        } catch (Exception $e) {
            $output->writeln('<fg=red;options=bold>Error:</>' . $e->getMessage());

            return self::FAILURE;
        }

        $this->checkResult($return, $output);

        if($options['raw'])
        {
            $output->writeln($return);
        }
        else
        {
            $this->writeTable($return['list'][0]['records'], $output);
        }

        return self::SUCCESS;
    }

    /**
     * @return AbstractEndpoint
     */
    protected function getEndpoint(): AbstractEndpoint
    {
        return $this->webapi->getBindEndpoint();
    }

    /**
     * @param                 $data
     * @param OutputInterface $output
     */
    protected function writeTable($data, OutputInterface $output): void
    {
        $table = new Table($output);
        $table->setHeaders(['domain', 'type', 'ttl', 'value', 'rtype', 'active', 'cnameAlt']);

        foreach ($data as $item) {
            $table->addRow([
                               $item['domain'],
                               $item['type'],
                               $item['ttl'],
                               $item['value'],
                               $item['rtype'],
                               ($item['active'] ?? false) ? 'true' : 'false',
                               $item['cnameAlt'] ?? null,
                           ]);
        }
        $table->render();
    }

    /**
     * @param array           $options
     * @param OutputInterface $output
     */
    protected function OpCreate(array $options, OutputInterface $output): void
    {
    }

    /**
     * @param array           $options
     * @param OutputInterface $output
     */
    protected function OpUpdate(array $options, OutputInterface $output): void
    {
    }
}
