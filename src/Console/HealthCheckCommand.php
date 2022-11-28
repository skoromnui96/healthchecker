<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck\Console;

use Eglobal\Healthcheck\Service\HealthCheckService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

final class HealthCheckCommand extends Command
{
    protected static $defaultName = 'fx:health:check';

    private HealthCheckService $healthCheckService;

    public function __construct(HealthCheckService $healthCheckService)
    {
        parent::__construct();
        $this->healthCheckService = $healthCheckService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('services', InputArgument::IS_ARRAY)
            ->addOption(
                'stop_on_failure',
                's',
                InputOption::VALUE_NONE,
                'Stop next checks on first failure?'
            )
            ->setDescription('The command checks the availability of dependent services.')
            ->setHelp(
                <<<HELP
The command checks the availability of dependent services.
    <info>php %command.full_name% rabbit_mq,elasticsearch --stop_on_failure</info>
HELP
            );
    }

    protected function doExecute(InputInterface $input, SymfonyStyle $io): void
    {
        try {
            $services = $input->getArgument('services');
            $stopOnFailure = $input->getOption('stop_on_failure');
            $results = $this->healthCheckService->check($services, $stopOnFailure);

            foreach ($results as $result) {
                echo json_encode($result);
                $io->newLine(2);
            }

            $io->newLine();
            $io->note('Healthcheck was completed.');
        } catch (Throwable $exception) {
            $io->writeln($exception->getMessage());
        }
    }
}
