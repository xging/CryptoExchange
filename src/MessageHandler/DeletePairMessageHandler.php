<?php

namespace App\MessageHandler;

use App\Message\DeletePairMessage;
use App\Services\Interfaces\CurrencyPairInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeletePairMessageHandler
{
    public function __construct(private CurrencyPairInterface $deletePairCurrencyService)
    {
    }

    public function __invoke(DeletePairMessage $message): void
    {
        $content = $message->getMessage();
        $args    = $message->getArgs();
        echo "RemovePairMessage content: $content\n";
        $this->deletePairCurrencyService->execute($args);
    }
}
