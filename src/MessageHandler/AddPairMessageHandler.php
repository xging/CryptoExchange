<?php

namespace App\MessageHandler;

use App\Message\AddPairMessage;
use App\Services\Interfaces\CurrencyPairInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AddPairMessageHandler
{
    public function __construct(private CurrencyPairInterface $addPairCurrencyService)
    {
    }

    public function __invoke(AddPairMessage $message): void
    {
        $content = $message->getMessage();
        $args    = $message->getArgs();
        echo "AddPairMessage content: $content\n";
        $this->addPairCurrencyService->execute($args);
    }
}
