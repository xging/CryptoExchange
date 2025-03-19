<?php

namespace App\Command;

use App\Command\Interfaces\CommandArgsValidateInterface;
use App\Message\DeletePairMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:delete-pair', description: 'delete Currency pair from queue')]
final class DeletePairCommand extends Command implements CommandArgsValidateInterface
{
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
                $output->writeln('Usage: php bin/console app:remove-pair "<from_currency> <to_currency>"');
                return Command::FAILURE;
            }
            [$from, $to] = explode(' ', $argument);
            $argsFromConsole = [
                ['from_currency' => $from, 'to_currency' => $to],
                ['from_currency' => $to, 'to_currency' => $from]
            ];

            $this->messageBus->dispatch(new DeletePairMessage('DeletePairCommand', $argsFromConsole));
            $output->writeln("Removal of pair {$from} - {$to} have been sent to the queue for processing.");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Error occurred: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }

    public function validate(string $argument): bool
    {
        if (!$argument || count(explode(' ', $argument)) !== 2) {
            return false;
        }
        return true;
    }
}
