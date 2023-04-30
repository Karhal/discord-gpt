<?php

namespace Bot\Handler;

use Bot\Model\HistoryList;
use Bot\Model\Message;

class HistoryListHandler
{
    public function addMessageToHistory(HistoryList $historyList, Message $message): void
    {
        $historyList->list[$message->channelId][] = $message;

        if (HistoryList::MSG_HISTORY_COUNT === count($historyList->list[$message->channelId])) {
            array_shift($historyList->list[$message->channelId]);
        }
    }
}
