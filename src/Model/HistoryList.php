<?php

namespace Bot\Model;

class HistoryList
{
    public const MSG_HISTORY_COUNT = 20;

    public array $list = [];

    public function getChannelList(string $channel): array
    {
        if (array_key_exists($channel, $this->list)) {
            return $this->list[$channel];
        }

        return [];
    }
}
