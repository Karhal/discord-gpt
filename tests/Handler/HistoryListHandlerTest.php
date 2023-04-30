<?php

namespace Tests\Handler;

use Bot\Configuration;
use Bot\Handler\HistoryListHandler;
use Bot\Model\HistoryList;
use Bot\Model\Message;
use PHPUnit\Framework\TestCase;

class HistoryListHandlerTest extends TestCase
{
    public function testAddMessageToHistory()
    {
        $handler = new HistoryListHandler();
        $historyList = new HistoryList();
        $messages = $this->createMessage(HistoryList::MSG_HISTORY_COUNT);

        foreach($messages as $message) {
            $handler->addMessageToHistory($historyList, $message);
        }

        $this->assertLessThan(HistoryList::MSG_HISTORY_COUNT, count($historyList->getChannelList($message->channelId)));
    }

    private function createMessage(int $count = 1): \Generator
    {
        for($i=0; $i<$count; $i++) {
            $message = new Message();
            $message->author = sprintf('Bob_%d', $i);
            $message->message = 'Lorem Ipsum';
            $message->channelId = 1;
            $message->timestamp = (new \DateTime('yesterday'))->getTimestamp();

            yield $message;
        }
    }
}