<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Command;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\TransferException;
use Myracloud\WebApi\Endpoint\AbstractEndpoint;
use Myracloud\WebApi\Endpoint\CacheClear;
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
        $this->setDescription('CacheClear commands allows you to do a cache clear via Myra API.');
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
