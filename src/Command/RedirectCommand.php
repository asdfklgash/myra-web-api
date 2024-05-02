<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Command;


use DateTimeImmutable;
use GuzzleHttp\Exception\GuzzleException;
use Myracloud\WebApi\Endpoint\Enum\MatchEnum;
use Myracloud\WebApi\Endpoint\Enum\RedirectEnum;
use Myracloud\WebApi\Endpoint\Redirect;
use RuntimeException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RedirectCommand extends AbstractCrudCommand
{
    /**
     *
     */
    protected function configure(): void
    {
        $this->setName('myracloud:api:redirect');

        $this->addOption('source', null, InputOption::VALUE_REQUIRED, 'Source path', null);
        $this->addOption('dest', null, InputOption::VALUE_REQUIRED, 'destination path', null);

        $this->addOption('type', null, InputOption::VALUE_REQUIRED, 'Type of redirect (' . implode(',', RedirectEnum::values()) . ')', RedirectEnum::Redirect->value);
        $this->addOption('matchtype', null, InputOption::VALUE_REQUIRED, 'Type of substring matching (' . implode(',', MatchEnum::values()) . ')', MatchEnum::Prefix->value);


        $this->setDescription('Redirect commands allow you to edit Url Redirects.');
        $this->setHelp(sprintf(<<<'TAG'
Only passing fqdn without additional options will list all Redirect entries.

<fg=yellow>Example Listing all Redirect entries:</>
bin/console myracloud:api:redirect <fqdn>

<fg=yellow>Example creating a new Redirect entry:</>
bin/console myracloud:api:redirect <fqdn> -o create --source <pattern-to-match> --dest <target-url>  --type <redirection-type> --matchtype <type>

<fg=yellow>Example updating a existing Redirect entry:</>
bin/console myracloud:api:redirect <fqdn> -o update --id <id-from-list> --source <pattern-to-match> --dest <target-url> --type <redirection-type> --matchtype <matchtype>

<redirection-type> can be %s
<matchtype> can be %s

<fg=yellow>Example deleting a existing Redirect entry:</>
bin/console myracloud:api:redirect -o delete --id <id-from-list>
TAG
                , implode(',', RedirectEnum::values()), implode(',', MatchEnum::values()))
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
        $endpoint = $this->getEndpoint();
        if (empty($options['source'])) {
            throw new RuntimeException('You need to define source path via --source');
        }
        if (empty($options['dest'])) {
            throw new RuntimeException('You need to define destination path via --dest');
        }
        $type = RedirectEnum::tryFrom($options['type']??'');
        $matchType = MatchEnum::tryFrom($options['matchtype']??'');

        if (!$type) {
            throw new RuntimeException('--type has to be one of ' . implode(',', RedirectEnum::values()));
        }
        if (!$matchType) {
            throw new RuntimeException('--type has to be one of ' . implode(',', MatchEnum::values()));
        }

        $return = $endpoint->create(
            $options['fqdn'],
            $options['source'],
            $options['dest'],
            $type,
            $matchType
        );
        $this->handleTableReturn($return, $output);
    }

    protected function getEndpoint(): Redirect
    {
        return $this->webapi->getRedirectEndpoint();
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
            'Source',
            'Destination',
            'Type',
            'Subdomain',
            'MatchType',
        ]);

        foreach ($data as $item) {
            $table->addRow([
                $item['id']??null,
                $item['created'],
                $item['modified'],
                $item['source']??'',
                $item['destination']??'',
                RedirectEnum::tryFrom($item['type']??'')?->value,
                $item['subDomainName'],
                MatchEnum::tryFrom($item['matchingType']??'')?->value,
            ]);
        }
        $table->render();
    }

    /**
     * @param array           $options
     * @param OutputInterface $output
     * @throws GuzzleException
     */
    protected function OpUpdate(array $options, OutputInterface $output): void
    {
        $endpoint = $this->getEndpoint();
        $existing = $this->findById($options);

        $options['source'] ??= $existing['source'] ?? '';
        $options['dest'] ??= $existing['destination'] ?? '';
        $type = RedirectEnum::tryFrom($options['type']??$existing['type']??'');
        $matchType = MatchEnum::tryFrom($options['matchtype']??$existing['matchtype']??'');

        if (!$type) {
            throw new RuntimeException('--type has to be one of ' . implode(',', RedirectEnum::values()));
        }
        if (!$matchType) {
            throw new RuntimeException('--matchtype has to be one of ' . implode(',', MatchEnum::values()));
        }

        $return = $endpoint->update(
            $options['fqdn'],
            $options['id'],
            new DateTimeImmutable($existing['modified']),
            $options['source'],
            $options['dest'],
            $type,
            $matchType
        );

        $this->handleTableReturn($return, $output);
    }
}
