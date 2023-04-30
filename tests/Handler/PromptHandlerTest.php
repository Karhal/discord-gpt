<?php

namespace Tests\Handler;

use Bot\Configuration;
use Bot\Handler\HistoryListHandler;
use Bot\Handler\PromptHandler;
use Bot\Model\HistoryList;
use Bot\Model\Message;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class PromptHandlerTest extends TestCase
{
    public function testGeneratePromptFromHistory()
    {
        $conf = new Configuration();
        $handler = $this->getHandler();
        $historyListandler = new HistoryListHandler();

        $oldMessage = new Message();
        $oldMessage->author = 'Bob';
        $oldMessage->message = 'Bonne nuit';
        $oldMessage->channelId = 1;
        $oldMessage->timestamp = (new \DateTime('yesterday'))->getTimestamp();

        $message = new Message();
        $message->author = 'Bob';
        $message->channelId = 1;
        $message->message = 'Bonjour comment ça va ?';
        $message->timestamp = (new \DateTime())->getTimestamp();

        $historyList = new HistoryList();
        $historyListandler->addMessageToHistory($historyList, $message);

        $channelList = $historyList->getChannelList(1);
        $prompt = $handler->generatePromptFromHistory($channelList);

        $this->assertEquals(sprintf("Bob: %s\n%s:", $message->message, $conf->getConfig()['openai']['botExternalName']), $prompt, '');
    }

    private function getHandler(): PromptHandler
    {
        $conf = new Configuration();
        $logger = new Logger('name');
        $logger->pushHandler(new StreamHandler(__DIR__.'/../var/logs/output.log', Level::Info));

        return new PromptHandler($conf);
    }
}
