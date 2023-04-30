<?php

namespace Bot;

use Bot\Client\OpenAIClient;
use Bot\Handler\BotHandler;
use Bot\Handler\HistoryListHandler;
use Bot\Handler\MessageHandler;
use Bot\Handler\PromptHandler;
use Bot\Model\HistoryList;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class Application
{
    public Logger $logger;
    public Configuration $configuration;
    public PromptHandler $promptHandler;
    public HistoryListHandler $historyListHandler;
    public BotHandler $botHandler;
    public OpenAIClient $openAIClient;
    public HistoryList $historyList;
    public MessageHandler $messageHandler;

    public function __construct()
    {
        $this->logger = new Logger('name');
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/../var/logs/output.log', Level::Info));
        $this->configuration = new Configuration();
        $this->promptHandler = new PromptHandler($this->configuration);
        $this->historyListHandler = new HistoryListHandler();
        $this->botHandler = new BotHandler($this->configuration);
        $this->openAIClient = new OpenAIClient($this->configuration, $this->logger, $this->promptHandler);
        $this->historyList = new HistoryList();
        $this->messageHandler = new MessageHandler($this->configuration);
    }
}
