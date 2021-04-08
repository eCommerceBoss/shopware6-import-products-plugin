<?php declare(strict_types=1);

namespace Sas\SyncerModule\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sas\SyncerModule\Service\WritingProductData;
use Shopware\Core\Framework\Context;
use Doctrine\DBAL\Connection;

class SyncerCommand extends Command
{
    // Command name
    protected static $defaultName = 'syncer-commands:start';

    private $writingData;

    private $connection;

    public function __construct(WritingProductData $writingData, Connection $connection)
    {
        parent::__construct();
        $this->writingData = $writingData;
        $this->connection = $connection;
    }

    // Provides a description, printed out in bin/console
    protected function configure(): void
    {
        $this->setDescription('Does something very special.');
    }

    // Actual code executed in the command
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $context = Context::createDefaultContext();
        $this->writingData->writeData($context, $this->connection);
        $output->writeln('It works!');

        // Exit code 0 for success
        return 0;
    }
}