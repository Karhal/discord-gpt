<?php

namespace Bot\Handler;

use Bot\Configuration;
use Bot\Utils\DateUtils;

class PromptHandler
{
    private array $config;
    private bool $ignoreMyself;

    public function __construct(Configuration $config)
    {
        $this->config = $config->getConfig()['openai'];
        $this->ignoreMyself = $config->getConfig()['app']['ignoreMyself'];
    }

    public function generatePromptFromHistory(array $channelList): array
    {
        $start = 0;
        $prompt = [];
        $listCount = count($channelList);
        $externalName = $this->config['botExternalName'];
        $back = $this->config['historyGeneratedPrompt'];

        if ($listCount > $back) {
            $start = $listCount - $back;
        }

        for ($i = $start; $i < $listCount; ++$i) {
            if ($this->ignoreMyself && $channelList[$i]->author === $externalName
                 || DateUtils::isTimestampExpired(time() - $this->config['timeZoneDelta'], $channelList[$i]->timestamp, $this->config['historyTtl'])) {
                continue;
            }
            $actor = 'user';
            $nickname = $channelList[$i]->author;
            $message = "($nickname): {$channelList[$i]->message}\n";
            if ($channelList[$i]->isAssistant) {
                $actor = 'assistant';
                $nickname = '';
                $message = "{$channelList[$i]->message}\n";
            }
            $prompt[] = ['role' => $actor, 'content' => "$message"];
        }

        return $prompt;
    }

    public function extractPromptFileFromChannelList($channelList, $messageId): void
    {
        $back = intval($this->config['historyGeneratedPrompt']);

        for ($i = 0; $i < count($channelList); ++$i) {
            if ($channelList[$i]->messageId != $messageId || 0 === $i) {
                continue;
            }

            $prompt = '';
            for ($j = $back; $j > 0; --$j) {
                if (!array_key_exists($i - $j, $channelList)) {
                    continue;
                }
                $prompt .= "{$channelList[$i - $j]->author}: {$channelList[$i - $j]->message}\n";
            }

            $prompt .= $this->config['botExternalName'].':';
            $promptLine = $this->generatePromptObject($prompt, $channelList[$i]->message);
            file_put_contents(__DIR__.'/../../data/extracted_prompts.jsonl', json_encode($promptLine)."\n", FILE_APPEND);

            return;
        }
    }

    private function generatePromptObject(string $prompt, string $completion): \stdClass
    {
        $promptLine = new \stdClass();
        $promptLine->prompt = $prompt;
        $promptLine->completion = $completion.PHP_EOL;

        return $promptLine;
    }
}
