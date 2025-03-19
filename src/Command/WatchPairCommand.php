<?php

namespace App\Command;

use App\Command\Interfaces\CommandArgsValidateInterface;
use App\Message\WatchPairMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:watch-pair', description: 'Watch and process currency pairs')]
final class WatchPairCommand extends Command implements CommandArgsValidateInterface
{
    use HandleTrait;
    protected static $defaultName = 'app:watch-pair';

    public function __construct(private MessageBusInterface $messageBus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('argument', InputArgument::OPTIONAL, 'args');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $argument = $input->getArgument('argument');


            if (!$this->validate($argument)) {
                $output->writeln('Usage: php bin/console app:watch-pair"');
                return Command::FAILURE;
            }

            $message = new WatchPairMessage('WatchPairCommand');
            $result = $this->handle($message);
            $output->writeln("{$result}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Error occurred: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }

    public function validate(?string $argument = null): bool
    {
        if (!empty($argument)) {
            return false;
        }
        return true;
    }
}
