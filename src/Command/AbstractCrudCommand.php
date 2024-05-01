<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Command;


use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\TransferException;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractCrudCommand extends AbstractCommand
{
    protected const OPERATION_CREATE = 'create';
    protected const OPERATION_DELETE = 'delete';
    protected const OPERATION_LIST = 'list';
    protected const OPERATION_UPDATE = 'update';
    protected const OPERATION_EXPORT = 'export';
    protected static array $operations = [
        self::OPERATION_UPDATE,
        self::OPERATION_CREATE,
        self::OPERATION_DELETE,
        self::OPERATION_LIST,
        self::OPERATION_EXPORT,
    ];

    protected function configure(): void
    {
        parent::configure();
        $this->addOption('operation', 'o', InputOption::VALUE_REQUIRED, '', self::OPERATION_LIST);
        $this->addOption('page', null, InputOption::VALUE_REQUIRED, 'Page to show when listing objects.', 1);
        $this->addOption('id', null, InputOption::VALUE_REQUIRED, 'Id to Update/Delete');
        $this->addArgument('fqdn', InputArgument::REQUIRED, 'Domain that should be used.');
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

            if (!in_array($options['operation'], self::$operations)) {
                $output->writeln('<fg=red;options=bold>Error:</> --operation must be one of ' . implode(',', self::$operations));

                return Command::INVALID;
            }
            switch ($options['operation']) {
                case self::OPERATION_LIST:
                    $this->OpList($options, $output);
                    break;
                case self::OPERATION_CREATE:
                    $this->OpCreate($options, $output);
                    break;
                case self::OPERATION_UPDATE:
                    $this->OpUpdate($options, $output);
                    break;
                case self::OPERATION_DELETE:
                    $this->OpDelete($options, $output);
                    break;
                case self::OPERATION_EXPORT:
                    $this->OpExport($options, $output);
                    break;
            }
        } catch (TransferException $e) {
            $this->handleTransferException($e, $output);

            return Command::FAILURE;
        } catch (Exception $e) {
            $output->writeln('<fg=red;options=bold>Error:</>' . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * @param array           $options
     * @param OutputInterface $output
     */
    protected function OpList(array $options, OutputInterface $output): void
    {
        $endpoint = $this->getEndpoint();
        $return   = $endpoint->getList($options['fqdn'], $options['page']);
        $this->checkResult($return, $output);
        $this->writeTable($return['list'], $output);
        if ($output->isVerbose()) {
            print_r($return);
        }
    }

    /**
     * @param                 $data
     * @param OutputInterface $output
     */
    abstract protected function writeTable($data, OutputInterface $output): void;

    /**
     * @param array           $options
     * @param OutputInterface $output
     */
    abstract protected function OpCreate(array $options, OutputInterface $output): void;

    /**
     * @param array           $options
     * @param OutputInterface $output
     */
    abstract protected function OpUpdate(array $options, OutputInterface $output): void;

    /**
     * @param array           $options
     * @param OutputInterface $output
     * @throws GuzzleException
     */
    protected function OpDelete(array $options, OutputInterface $output): void
    {
        if ($options['id'] == null) {
            throw new RuntimeException('You need to define the id of the object to delete via --id');
        }

        $endpoint = $this->getEndpoint();
        $existing = $this->findById($options);

        $return = $endpoint->delete($options['fqdn'], $options['id'], new DateTime($existing['modified']));
        $this->handleDeleteReturn($return, $output);
    }

    /**
     * @param array $options
     * @return array
     */
    protected function findById(array $options): array
    {
        if ($options['id'] == null) {
            throw new RuntimeException('You need to define the id of the object via --id');
        }
        $endpoint = $this->getEndpoint();
        $return   = $endpoint->getList($options['fqdn'], $options['page']);
        foreach ($return['list'] as $item) {
            if ($item['id'] == $options['id']) {
                return $item;
            }
        }
        throw new RuntimeException('Could not find an object with the passed id.');
    }

    /**
     * @param                 $return
     * @param OutputInterface $output
     */
    protected function handleDeleteReturn($return, OutputInterface $output): void
    {
        $this->checkResult($return, $output);
        $this->writeTable($return['targetObject'], $output);

        if (count($return['targetObject']) == 0) {
            $output->writeln('<fg=yellow;options=bold>Notice:</> No objects where deleted. Did you pass a valid Id?');
        }

        if ($output->isVerbose()) {
            print_r($return);
        }
    }

    /**
     * @param array           $options
     * @param OutputInterface $output
     * @throws Exception
     */
    protected function OpExport(array $options, OutputInterface $output): void
    {
        $date     = new DateTime();
        $endpoint = $this->getEndpoint();
        $return   = $endpoint->getList($options['fqdn'], $options['page']);
        $this->checkResult($return, $output);
        $yaml     = Yaml::dump($return['list']);
        $header   = '# ' . $options['fqdn'] . "\n";
        $header   .= '# ' . $endpoint->getEndPoint() . "\n";
        $header   .= '# ' . $date->format('c') . "\n";
        $filename = 'export_' . $endpoint->getEndPoint() . '_' . str_replace(':', '-', $options['fqdn']) . '_' . $date->format('Ymd_His') . '.yml';
        file_put_contents($filename, $header . $yaml);
        $output->writeln('Exported ' . count($return['list']) . ' entries to ' . $filename);
    }

    /**
     * @param                 $return
     * @param OutputInterface $output
     */
    protected function handleTableReturn($return, OutputInterface $output): void
    {
        $this->checkResult($return, $output);
        $this->writeTable($return['targetObject'], $output);
        if ($output->isVerbose()) {
            print_r($return);
        }
    }
}
