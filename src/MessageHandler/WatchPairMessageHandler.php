<?php

namespace App\MessageHandler;

use App\Message\WatchPairMessage;
use App\Services\WatchCurrencyPairService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class WatchPairMessageHandler
{
    public function __construct(private WatchCurrencyPairService $watchPairCurrencyService) {}

    public function __invoke(WatchPairMessage $message): void
    {
        $content = $message->getMessage();
        echo "WatchPairMessage content: $content\n";
        $this->watchPairCurrencyService->execute();
    }
}
