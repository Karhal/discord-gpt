<?php

namespace Bot\Handler;

use Bot\Configuration;
use Bot\Model\Message;

class BotHandler
{
    private $configuration;
    private ?int $lastMessageTimestamp = null;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration->getConfig();
    }

    public function shouldIAnswer(string $userId, Message $message): bool
    {
        $rand = mt_rand(1, 100);
        $randBool = $rand <= $this->configuration['app']['chanceToAddOneMoreAnswer'];
        if ($userId === $message->authorId) {
            if ($randBool) {
                $rand = mt_rand(1, 100);

                return true;
            }

            return false;
        }

        $content = strtolower($message->message);

        if (preg_match('/^(http|https):\/\//', $content)) {
            return false;
        }

        if ($this->configuration['app']['mentionsOnly']
        && !preg_match('/\b('.$this->configuration['openai']['botExternalName'].'|\?)\b/i', $content)) {
            return false;
        }

        $rule = implode('|', $this->configuration['app']['reactionWords']);
        if (!preg_match_all('/\b(?:'.strtolower($rule).')\b|\?/', $content)) {
            return false;
        }

        if (null !== $this->lastMessageTimestamp && time() - $this->lastMessageTimestamp < $this->configuration['app']['slowModeTime']) {
            return false;
        }
        $this->lastMessageTimestamp = time();

        return true;
    }
}
